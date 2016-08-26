<big><strong>Purpose:</strong></big>
<br/>
A better way to include css and js in html.
<br/>
<br/>
<big><strong>How to use:</strong></big>
<br/>
<strong>Initialization:</strong>
<br/>
CSS_URL - CSS base url.
<br/>
JS_URL - JS base url.
<br/>
VERSION - Current version.
<br/>
<code>
$asset_manager = new AssetManager(CSS_URL, JS_URL, VERSION);
</code>
<br/>
<strong>Add assets:</strong>
<br/>
addHeaderCSS, addFooterCSS params:
<br/>
1. css file name / url.
<br/>
2. media (screen, print, etc.) default is all.
<br/>
3. dependency - any css before which the current css should be included.
<br/>
addHeaderJS, addFooterJS params:
<br/>
1. js file name / url.
<br/>
2. dependency - any js before which the current js should be included.
<br/>
<code>
$asset_manager->addHeaderCSS('bootstrap.min.css');
<br/>
$asset_manager->addFooterCSS('common.css');
<br/>
$asset_manager->addHeaderJS('jquery.1.11.2.min.js');
<br/>
$asset_manager->addFooterJS('common.js');
<br/>
$asset_manager->addFooterJS('bootstrap.min.js', 'common.js'); Here bootstrap.min.js will be rendered before common.js<br/>
$asset_manager->removeCSS('common.css');<br/>
$asset_manager->removeJS('common.js');
</code>
<br/>
<strong>Render:</strong>
<br/>
<code>
$asset_manager->renderHeaderAssets(); Ideally should be called in head tag.<br/>
$asset_manager->renderFooterAssets(); Ideally should be called in footer just before closing of body tag.
</code>
