<?php

namespace AuthComponent\View;

	/**
	* An interface classes need to implement to be rendered togheter with the layout class.
	*/
interface View {
  public function getHTMLString();
}

?>