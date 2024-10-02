{{--
    Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
    See the LICENCE file in the repository root for full licence text.
--}}
@extends('master', ['titlePrepend' => $product->name])

@section('content')
    @include('store.header')

    <form
        action="{{ route('store.cart.store', ['add' => true]) }}"
        class="osu-page osu-page--store"
        data-remote
        id="product-form"
        method="POST"
    >
        @csrf
        <div class="product-box product-box--header" {!! background_image($product->header_image) !!}></div>

        <div class="store-page">
            <h1 class="store-text store-text--title">{{ $product->name }}</h1>

            @if($product->custom_class && View::exists("store.products.{$product->custom_class}"))
                {!! markdown($product->description, 'store') !!}
                @include("store.products.{$product->custom_class}")
            @else
                <div class="store-page__product">
                    <div>
                        <div class="gallery-previews">
                            @foreach($product->images() as $i => $image)
                                @php
                                    $imageSize = fast_imagesize($image[1], "store_product:{$product->getKey()}");
                                @endphp
                                <a
                                    class="gallery-previews__item js-gallery"
                                    data-width="{{ $imageSize[0] ?? null }}"
                                    data-height="{{ $imageSize[1] ?? null }}"
                                    data-gallery-id="product-{{ $product->product_id }}"
                                    data-index="{{ $i }}"
                                    href="{{ $image[1] }}"
                                    style="background-image: url('{{ $image[1] }}');"
                                    data-visibility="{{ $loop->first ? '' : 'hidden' }}"
                                ></a>
                            @endforeach
                        </div>
                        <div class="gallery-thumbnails">
                            @foreach($product->images() as $i => $image)
                                <a
                                    href="#"
                                    style="background-image: url('{{ $image[0] }}');"
                                    class="
                                        gallery-thumbnails__item
                                        js-gallery-thumbnail
                                        {{ $loop->first ? 'js-gallery-thumbnail--active' : '' }}
                                    "
                                    data-gallery-id="product-{{ $product->product_id }}"
                                    data-index="{{ $i }}"
                                ></a>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        {!! markdown($product->description, 'store') !!}

                        <p class="store-text store-text--price">{{ currency($product->cost) }}</p>
                        @if($product->requiresShipping())
                            <p class="store-text store-text--price-note">excluding shipping fees</p>
                        @endif

                        @if($product->types())
                            @foreach($product->types() as $type => $values)
                                @if (count($values) === 1)
                                    {{-- magic property --}}
                                    <input type="hidden" name="item[extra_data][{{ $type }}]" value="{{ array_keys($values)[0] }}" />
                                @else
                                    <div class="input-container input-container--select input-container--store">
                                        <label
                                            class="input-container__label"
                                            for="select-product-{{ $type }}"
                                        >
                                            {{ $type }}
                                        </label>

                                        <select
                                            id="select-product-{{ $type }}"
                                            class="input-text js-url-selector"
                                            data-keep-scroll="1"
                                        >
                                            @foreach($values as $value => $product_id)
                                                <option
                                                    {{ $product_id === $product->product_id ? 'selected' : '' }}
                                                    value="{{ route('store.products.show', $product_id) }}"
                                                >
                                                    {{ $value }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            @endforeach
                        @endif

                        @if($product->inStock())
                            <div class="input-container input-container--select input-container--store">
                                <input type="hidden" name="item[product_id]" value="{{ $product->product_id }}" />
                                <label for="item[quantity]" class="input-container__label">
                                    {{ osu_trans('store.order.item.quantity') }}
                                </label>

                                <select
                                    class="js-store-item-quantity input-text"
                                    name="item[quantity]"
                                >
                                    @foreach (product_quantity_options($product) as $option)
                                        <option value="{{ $option['value'] }}">
                                            {{ $option['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @elseif ($product->inStock(1, true))
                            {{ osu_trans('store.product.stock.out_with_alternative') }}
                        @else
                            {{ osu_trans('store.product.stock.out') }}
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="store-page store-page--footer" id="add-to-cart">
            @if ($product->inStock())
                <button
                    class="btn-osu-big btn-osu-big--store-action js-login-required--click js-store-add-to-cart"
                    type="submit"
                    {{ in_array($product->custom_class, App\Models\Store\Product::BUTTON_DISABLED, true) ? 'disabled' : '' }}
                >
                    {{ osu_trans('store.product.add_to_cart') }}
                </button>
            @elseif (!$requestedNotification)
                <a
                    class="btn-osu-big btn-osu-big--store-action js-login-required--click"
                    href="{{ route('store.notification-request', ['product' => $product->product_id]) }}"
                    data-remote="true"
                    data-method="POST"
                >
                    {{ osu_trans('store.product.notify') }}
                </a>
            @endif

            @if($requestedNotification && !$product->inStock())
                <div class="store-notification-requested-alert">
                    <span class="far fa-check-circle store-notification-requested-alert__icon"></span>
                    <p class="store-notification-requested-alert__text">
                        {!! osu_trans('store.product.notification_success', [
                            'link' => link_to(
                                route('store.notification-request', ['product' => $product->product_id]),
                                osu_trans('store.product.notification_remove_text'),
                                ['data-remote' => 'true', 'data-method' => 'DELETE'],
                            ),
                        ]) !!}
                    </p>
                </div>
            @endif
        </div>

    </form>
@endsection
