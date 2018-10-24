<?php
namespace Administration\Model;

class MenuBar
{
	function _initParam($param)
	{
		if (empty($param["jsfile"])) $param["jsfile"] = "MenuBar.js";
		if (empty($param["topparentnodecssclass"])) $param["topparentnodecssclass"] = "menuButton";
		if (empty($param["parentnodecssclass"])) $param["parentnodecssclass"] = "menu";
		if (empty($param["nodecssclass"])) $param["nodecssclass"] = "menuItem";
		if (empty($param["disabledcssclass"])) $param["disabledcssclass"] = "disabled";
		if (empty($param["separatorcssclass"])) $param["separatorcssclass"] = "menuItemSep";
		if (empty($param["cssclass"])) $param["cssclass"] = "menuBar";
		if (empty($param["topdivid"])) $param["topdivid"] = "menubar_id";
		if (empty($param["menuobject"])) $param["menuobject"] = "MenuBar";
		if (empty($param["horizontal"])) $param["horizontal"] = true;
		
		return $param;
	}
	
	function __createMenu($menu, $parentid, $cssclass, $menuobject)
	{
		$_menu = '';
		$childmenu = '';
		if (!is_null($menu)) {
			$haschild = false;
			$first = true;
			$double = false;
			$last = false;
			$countMenu = count($menu);
			for ($i = 0; $i < $countMenu; $i++) {
				if ($i == ($countMenu-1)) {
					$last = true;
				} else if ($first && ($i > 0)) {
					if ($menu[$i]['caption'] != '---')  {
						$first = false;
					}
				} else if (!$first && ($i > 0) && ($menu[$i]['caption'] == '---')) {
					$found = false;
					for ($j = $i; $j < $countMenu; $j++) {
						if ($menu[$j]['caption'] != '---') {
							$found = true;
							break;
						}
					}
					if (!$found) {
						$last = true;
					}
				}
				if (!empty($menu[$i]['node']) && is_array($menu[$i]['node'])) {
					if ($first) $first = false;
					if ($double) $double = false;
					$node = $menu[$i]['node'];
					$href = '#';
					if (!empty($menu[$i]['id'])) {
						$divid = $menu[$i]['id'];
					} else {
						$divid = $parentid.sprintf("%03d",$i);
					}
					$haschild = true;
					if (empty($menu[$i]['disabled'])) {
						$class = $cssclass['node'];
						$_menu .= '<a class="' . $class . '" href="' . $href . '" onclick="return false;" onmouseover="' . $menuobject . ".menuItemMouseover(event, '" . $divid . "');" . '"><span style="padding-right: 13px;" class="menuItemText">' . $menu[$i]['caption'] . '</span><span class="menuItemArrow">&#9654</span></a>' . "\n";
					} else {
						$class = $cssclass['disabled'];
						$_menu .= '<a class="' . $class . '" href="#" onmouseover="' . $menuobject . ".menuItemMouseover(event, null);" . '"><span style="padding-right: 13px;" class="menuItemText">' . $menu[$i]['caption'] . '</span><span class="menuItemArrow">&#9654</span></a>' . "\n";
					}
				} else {
					$node = NULL;
					if (empty($menu[$i]['href'])) {
						$href = '';
					} else {
						$href = $menu[$i]['href'];
					}
					if (empty($menu[$i]['onclick'])) {
						$onclick = '';
					} else {
						$onclick = ' onclick="' . $menu[$i]['onclick'] . '"';
					}
					$divid = $parentid;
					if (empty($menu[$i]['disabled'])) {
						$class = $cssclass['node'];
					} else {
						$class = $cssclass['disabled'];
					}
					if ($menu[$i]['caption'] == "---") {
						if (!$first && !$last && !$double) {
							$_menu .= '<div class="' . $cssclass['separator'] . '"></div>' . "\n";
							$double = true;
						}
					} else {
						if ($first) $first = false;
						if ($double) $double = false;
						$_menu .= '<a class="' . $class . '" href="' . $href . '"' . $onclick . '>' . $menu[$i]['caption'] . '</a>' . "\n";
					}
				}
				if (empty($menu[$i]['disabled'])) {
					$class = $cssclass["parent"];
					$childmenu .= $this->__createMenu($node, $divid, $cssclass, $menuobject);
				} else {
					$haschild = false;
					$class = $cssclass["disabled"];
				}
			}
			if ($haschild) {
				$_menu = '<div style="left: 14px; top: 265px; visibility: hidden;" id="' . $parentid . '" class="' . $class . '" onmouseover="' . $menuobject . '.menuMouseover(event)">' . "\n" . $_menu;
			} else {
				$_menu = '<div style="left: 14px; top: 265px; visibility: hidden;" id="' . $parentid . '" class="' . $class . '">' . "\n" . $_menu;
			}
			$_menu .= "</div>\n";
			$_menu .= $childmenu;
		}
		
		return $_menu;
	}
	
	function _createHorizontalMenu($menu, $param)
	{
		$topmenu = '';
		$childmenu = '';
		if (!is_null($menu)) {
			// Check ID First
			$countMenu = count($menu);
			for ($i = 0; $i < $countMenu; $i++) {
				$cssclass = array("parent" => $param["parentnodecssclass"], "node" => $param["nodecssclass"], "disabled" => $param["disabledcssclass"], "separator" => $param["separatorcssclass"]);
				if (empty($menu[$i]['disabled'])) {
					$class = $param["topparentnodecssclass"];
					if (!empty($menu[$i]['node']) && is_array($menu[$i]['node'])) {
						$node = $menu[$i]['node'];
						if (!empty($menu[$i]['id'])) {
							$divid = $menu[$i]['id'];
						} else {
							$divid = 'menu_bar'.sprintf("%03d", $i);
						}
						$href = '#';
						$onclick = 'return '.$param['menuobject'].".buttonClick(event, '$divid');";
						$onmouseover = $param['menuobject'].".buttonMouseover(event, '$divid');";
					} else {
						$node = NULL;
						if (empty($menu[$i]['href'])) {
							$href = '';
						} else {
							$href = $menu[$i]['href'];
						}
						if (!empty($menu[$i]['id'])) {
							$divid = $menu[$i]['id'];
						} else {
							$divid = 'menu_bar'.sprintf("%03d", $i);
						}
						$onclick = 'return true';
						$onmouseover = $param['menuobject'].".buttonMouseover(event, null);";
					}
					$topmenu .= '<a href="' . $href . '" class="' . $class . '" onclick="' . $onclick . '" onmouseover="' . $onmouseover . '">' . $menu[$i]['caption'] . '</a>';
					$childmenu .= $this->__createMenu($node, $divid, $cssclass, $param['menuobject']);
				} else {
					$class = $param["disabledcssclass"];
					$topmenu .= '<a href="#" class="' . $class . '">' . $menu[$i]['caption'] . '</a>';
//					$childmenu .= $this->__createMenu($node, $divid, $cssclass, $param['menuobject']);
				}
			}
			$topmenu .= "\n".$childmenu;
		}
		
		return $topmenu;
	}
	
	public function MenuBar($menu = NULL,$param = NULL)
	{
		$cssfile=$jsfile=$topparentnodecssclass=$parentnodecssclass=$nodecssclass=$disabledcssclass=$cssclass=$horizontal=$topdivid=NULL;
		$param = $this->_initParam($param);
		extract($param); // cssfile, jsfile, topparentnodecssclass, parentnodecssclass, nodecssclass, disabledcssclass, cssclass, horizontal
		
		// build the element
		$xhtml = '<script type="text/javascript" src="/js/' . $jsfile . '"></script>' . "\n";
		if ($horizontal) {
			// Horizontal
			$xhtml .= '<div id="'.$topdivid.'" class="'.$cssclass.'">';
			$xhtml .= $this->_createHorizontalMenu($menu,$param);
			$xhtml .= '</div>' . "\n";
		} else {
			// Vertical
			$xhtml = '';
		}
		
		return $xhtml;
	}
}
