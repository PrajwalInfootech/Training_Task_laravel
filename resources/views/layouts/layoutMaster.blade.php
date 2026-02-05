@isset($pageConfigs)
{!! Helper::updatePageConfig($pageConfigs) !!}
@endisset
@vite(['resources/assets/js/app.js'])

@php
$configData = Helper::appClasses();

if (request()->routeIs('register*')) {
    $configData['layout'] = 'blank';
}
if (request()->routeIs('login*')) {
    $configData['layout'] = 'blank';
}
@endphp

@isset($configData["layout"])
@include(
  ($configData["layout"] === 'horizontal') ? 'layouts.horizontalLayout' :
  (($configData["layout"] === 'blank') ? 'layouts.blankLayout' :
  (($configData["layout"] === 'front') ? 'layouts.layoutFront' :
  'layouts.contentNavbarLayout'))
)
@endisset
