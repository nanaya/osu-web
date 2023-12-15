{{--
    Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
    See the LICENCE file in the repository root for full licence text.
--}}
<script id="json-current-user" type="application/json">
    {!!
        $currentUser === null
            ? '{}'
            : json_encode(json_item($currentUser, new App\Transformers\CurrentUserTransformer()));
    !!}
</script>
