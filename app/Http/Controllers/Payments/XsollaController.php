<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace App\Http\Controllers\Payments;

use App\Exceptions\InvalidSignatureException;
use App\Exceptions\Store\OrderException;
use App\Libraries\Payments\XsollaPaymentProcessor;
use App\Libraries\Payments\XsollaSignature;
use App\Libraries\Payments\XsollaUserNotFoundException;
use App\Models\Store\Order;
use Auth;
use Exception;
use Illuminate\Http\Request as HttpRequest;
use Request;
use Xsolla\SDK\API\PaymentUI\TokenRequest;
use Xsolla\SDK\API\XsollaClient;

class XsollaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['callback']]);
        $this->middleware('check-user-restricted', ['except' => ['callback']]);
        $this->middleware('verify-user', ['except' => ['callback']]);

        parent::__construct();
    }

    public function token()
    {
        $projectId = $GLOBALS['cfg']['payments']['xsolla']['project_id'];
        $user = Auth::user();
        $order = Order::whereOrderNumber(request('orderNumber'))
            ->whereCanCheckout()
            ->first();

        if ($order === null) {
            return;
        }

        $tokenRequest = new TokenRequest($projectId, (string) $user->user_id);
        $tokenRequest
            ->setSandboxMode($GLOBALS['cfg']['payments']['sandbox'])
            ->setExternalPaymentId($order->getOrderNumber())
            ->setUserEmail($user->user_email)
            ->setUserName($user->username)
            ->setPurchase($order->getTotal(), 'USD')
            ->setCustomParameters([
                'subtotal' => $order->getSubtotal(),
                'shipping' => $order->shipping,
                'order_id' => $order['order_id'],
            ]);

        $xsollaClient = XsollaClient::factory([
            'merchant_id' => $GLOBALS['cfg']['payments']['xsolla']['merchant_id'],
            'api_key' => $GLOBALS['cfg']['payments']['xsolla']['api_key'],
        ]);

        // This will be used for XPayStationWidget options.
        return [
            'access_token' => $xsollaClient->createPaymentUITokenFromRequest($tokenRequest),
            'sandbox' => $GLOBALS['cfg']['payments']['sandbox'],
        ];
    }

    // Called by xsolla after payment is approved by user.
    public function callback(HttpRequest $request)
    {
        $params = static::extractParams($request);
        $signature = new XsollaSignature($request);
        $processor = new XsollaPaymentProcessor($params, $signature);

        try {
            if ($processor->isSkipped()) {
                return ['ok'];
            }

            $processor->run();
        } catch (OrderException $exception) {
            log_error($exception);

            return $this->errorResponse(
                'A validation error occured while running the transaction',
                'INVALID',
                422
            );
        } catch (InvalidSignatureException $exception) {
            log_error($exception);
            // xsolla expects INVALID_SIGNATURE
            return $this->errorResponse('The signature is invalid.', 'INVALID_SIGNATURE', 422);
        } catch (XsollaUserNotFoundException $exception) {
            return $this->errorResponse('INVALID_USER', 'INVALID_USER', 404);
        } catch (Exception $exception) {
            log_error($exception);

            // status code needs to be a 4xx code to make Xsolla an error to the user.
            return $this->errorResponse('Something went wrong.', 'FATAL_ERROR', 422);
        }
    }

    // After user has approved payment and redirected here by xsolla
    public function completed()
    {
        $orderNumber = Request::input('foreignInvoice') ?? '';
        $order = Order::whereOrderNumber($orderNumber)->firstOrFail();

        return redirect(route('store.invoice.show', ['invoice' => $order->getKey(), 'thanks' => 1]));
    }

    private function errorResponse(string $message, string $code, int $status)
    {
        return response()->json([
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
        ], $status);
    }
}
