<?php
/**
 * Asset Manager - Library to manage css and js includes
 * @version 1.0
 * @author Deovrat Jalgaonkar <jalgaonkar.deovrat@gmail.com>
*/
class AssetManager
{
	protected $css_base_url;
	protected $js_base_url;
	protected $version;
	protected $assets;

	public function __construct($css_base_url, $js_base_url, $version = 0)
	{
		$this->resetAssets();
		$this->setCSSBaseURL($css_base_url);
		$this->setJSBaseURL($js_base_url);
		$this->setVersion($version);
	}

	public function resetAssets()
	{
		$this->assets = array(
			"header" => array(
				"css" => array(),
				"js" => array()
			),
			"footer" => array(
				"css" => array(),
				"js" => array()
			)
		);
	}

	public function setCSSBaseURL($css_base_url)
	{
		$this->css_base_url = rtrim($css_base_url, " /");
	}

	public function setJSBaseURL($js_base_url)
	{
		$this->js_base_url = rtrim($js_base_url, " /");
	}

	public function getVersion()
	{
		return $this->version;
	}

	public function setVersion($version)
	{
		$this->version = $version;
	}

	protected function getAssetIndex($assets, $name)
	{
		if(is_array($assets) && !empty($name))
		{
			foreach($assets as $index => $asset)
			{
				if($asset["name"] == $name)
				{
					return $index;
				}
			}
		}

		return -1;
	}

	protected function isAssetProcessed($assets, $name)
	{
		$index = $this->getAssetIndex($assets, $name);

		return ($index != -1 && $assets[$index]["processed"]);
	}

	protected function getCSSURL($css)
	{
		if($css["calculate_url"])
		{
			return $this->css_base_url . "/" . $css["name"] . "?ver=" . $this->getVersion();
		}

		return $css["name"] . "?ver=" . $this->getVersion();
	}

	protected function getJSURL($js)
	{
		if($js["calculate_url"])
		{
			return $this->js_base_url . "/" . $js["name"] . "?ver=" . $this->getVersion();
		}

		return $js["name"] . "?ver=" . $this->getVersion();
	}

	protected function getOrderedAssets($assets)
	{
		if(is_array($assets))
		{
			foreach($assets as $index => $asset)
			{
				$before_asset_index = $this->getAssetIndex($assets, $asset["before"]);

				/* asset is not already processed AND before asset exists in the assets array */
				if(!$assets[$index]["processed"] && $before_asset_index != -1)
				{
					unset($assets[$index]);

					$asset["processed"] = true;
					array_splice($assets, $before_asset_index, 0, array($asset));

					$assets = array_values($assets);

					return $this->getOrderedAssets($assets);
				}
			}
		}

		return $assets;
	}

	public function addHeaderCSS($name, $media = "", $before = "")
	{
		$css = array();

		$css["name"] = $name;
		$css["media"] = (!empty($media) ? $media : "all");
		$css["calculate_url"] = (strpos($name, "//") === false);
		$css["before"] = $before;
		$css["processed"] = false;

		$this->assets["header"]["css"][] = $css;
	}

	public function addHeaderJS($name, $before = "")
	{
		$js = array();

		$js["name"] = $name;
		$js["calculate_url"] = (strpos($name, "//") === false);
		$js["before"] = $before;
		$js["processed"] = false;

		$this->assets["header"]["js"][] = $js;
	}

	public function addFooterCSS($name, $media = "all", $before = "")
	{
		$css = array();

		$css["name"] = $name;
		$css["media"] = (!empty($media) ? $media : "all");
		$css["calculate_url"] = (strpos($name, "//") === false);
		$css["before"] = $before;
		$css["processed"] = false;

		$this->assets["footer"]["css"][] = $css;
	}

	public function addFooterJS($name, $before = "")
	{
		$js = array();

		$js["name"] = $name;
		$js["calculate_url"] = (strpos($name, "//") === false);
		$js["before"] = $before;
		$js["processed"] = false;

		$this->assets["footer"]["js"][] = $js;
	}

	public function removeCSS($name)
	{
		$css_index = $this->getAssetIndex($this->assets["header"]["css"], $name);
		
		/* css not found in header css array */
		if($css_index == -1)
		{
			$css_index = $this->getAssetIndex($this->assets["footer"]["css"], $name);

			/* css found in footer css array */
			if($css_index != -1)
			{
				unset($this->assets["footer"]["css"][$css_index]);
				$this->assets["footer"]["css"] = array_values($this->assets["footer"]["css"]);
			}
		}
		else
		{
			unset($this->assets["header"]["css"][$css_index]);
			$this->assets["header"]["css"] = array_values($this->assets["header"]["css"]);
		}
	}

	public function removeJS($name)
	{
		$js_index = $this->getAssetIndex($this->assets["header"]["js"], $name);
		
		/* js not found in header js array */
		if($js_index == -1)
		{
			$js_index = $this->getAssetIndex($this->assets["footer"]["js"], $name);

			/* js found in footer js array */
			if($js_index != -1)
			{
				unset($this->assets["footer"]["js"][$js_index]);
				$this->assets["footer"]["js"] = array_values($this->assets["footer"]["js"]);
			}
		}
		else
		{
			unset($this->assets["header"]["js"][$js_index]);
			$this->assets["header"]["js"] = array_values($this->assets["header"]["js"]);
		}
	}

	public function renderHeaderAssets()
	{
		if(!empty($this->assets["header"]["css"]))
		{
			foreach($this->getOrderedAssets($this->assets["header"]["css"]) as $css)
			{
				echo '<link type="text/css" rel="stylesheet" href="' . $this->getCSSURL($css) . '" media="' . $css["media"] . '" />' . PHP_EOL;
			}
		}

		if(!empty($this->assets["header"]["js"]))
		{
			foreach($this->getOrderedAssets($this->assets["header"]["js"]) as $js)
			{
				echo '<script type="text/javascript" src="' . $this->getJSURL($js) . '"></script>' . PHP_EOL;
			}
		}
	}

	public function renderFooterAssets()
	{
		if(!empty($this->assets["footer"]["css"]))
		{
			echo '<style type="text/css">';

			foreach($this->getOrderedAssets($this->assets["footer"]["css"]) as $css)
			{
				echo '@import url("' . $this->getCSSURL($css) . '") ' . $css["media"] . PHP_EOL;
			}

			echo '</style>' . PHP_EOL;
		}

		if(!empty($this->assets["footer"]["js"]))
		{
			foreach($this->getOrderedAssets($this->assets["footer"]["js"]) as $js)
			{
				echo '<script type="text/javascript" src="' . $this->getJSURL($js) . '"></script>' . PHP_EOL;
			}
		}
	}
}
