<?php

// +---------------------------------------------+
// |     Copyright  2010 - 2028 WeLive           |
// |     http://www.weentech.com                 |
// |     This file may not be redistributed.     |
// +---------------------------------------------+


//if(!defined('WELIVE')) die('File not found!');

class Radio {
	var $Name;			// Name of the radio list
	var $SelectedID;	// ID to be radio in the list
	var $CssClass;		// Stylesheet class name
	var $Attributes;	// Additional attributes for the element
	var $aOptions;		// Array for holding radio options

	// ItemAppend is a string that will be appended to each item after the label render.
	function AddOption($IdValue, $DisplayValue, $ItemAppend = '') {
		$this->aOptions[] = array('IdValue' => $IdValue, 'DisplayValue' => $DisplayValue, 'ItemAppend' => $ItemAppend);
	}

	function AddOptionArray($Array, $KeyPrefix) {
		while (list($key, $val) = each($Array)) {
			$this->AddOption($KeyPrefix.$key, $val);
		}
	}

	function Clear() {
		$this->Name = '';
		$this->SelectedID = 0;
		$this->CssClass = '';
		$this->Attributes = '';
		$this->aOptions = array();
	}

	function ClearOptions() {
		$this->aOptions = array();
	}

	function Get() {
		$sReturn = '';
		$OptionCount = count($this->aOptions);
		$i = 0;
		for ($i = 0; $i < $OptionCount ; $i++) {
			$sReturn .= '<input type="radio" name="'.$this->Name.'" '.$this->Attributes.' id="Radio_'.$this->Name.$i.'" value="'.$this->aOptions[$i]['IdValue'].'"';
			if ($this->aOptions[$i]['IdValue'] == $this->SelectedID) $sReturn .= ' checked="checked"';
			if ($this->CssClass != '') $sReturn .= ' class="'.$this->CssClass.'"';

			$sReturn .= ' /><label for="Radio_'.$this->Name.$i.'">'.$this->aOptions[$i]['DisplayValue'].'</label>'.$this->aOptions[$i]['ItemAppend'];
		}
		return $sReturn;
	}

	function Radio() {
		$this->Clear();
	}

	function Write() {
		echo($this->Get());
	}
}
?>