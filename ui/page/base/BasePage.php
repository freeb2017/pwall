<?php 

/**
 * Page is made of number of widgets.
 * 
 * Base class loads, process, renders widgets 
 * one by one. The rendering of widget can be 
 * overloaded by page class.
 *
 */
abstract class BasePage{
	
	private $widget_collections = array();
	private $scripts = false;
	
	public function __construct(){}	
	
	/**
	 * loads all the widgets which is needed to be called
	 * on page processing and rendering
	 */
	protected function callWidget( &$w_class ){
		
		$load_widget = true;
		
		if( $load_widget ){
			
			array_push( $this->widget_collections, $w_class );
		}
	}
	
	/**
	 * Returns the widgets which needs to be rendered
	 */
	public function getWidgets(){
		
		return $this->widget_collections;
	}
	
	/**
	 * Process all the widgets in following steps
	 * 
	 * 1) Loads all the widgets for page
	 * 2) Get the widgets that is needed to be called
	 * 3) Initailize all the widgets
	 * 4) Process all the widgets
	 */
	public function process(){
		
		//loads the widgets
		$this->loadWidgets();
		
		//get all the widgets...
		$widgets = $this->getWidgets();
		
		//Initialize all the widgets first...
		foreach( $widgets as $widget ) {
			$widget->init();		
			$widget->process();
		}
	}

	public function render(){

		global $module,$action,$page;
		
		require("ui/partials/head.php");

		$class = 'sidebar-mini';

		if($action == 'login')
			$class = 'login-page';
		else if($action == 'register')
			$class = 'register-page';

		echo "<body class='hold-transition skin-blue fixed $class'>";

		if(strtolower($module) != 'auth') {
			require("ui/partials/header.php");
			require("ui/partials/sidenav.php");
			require("ui/partials/section_start.php");
		}
		
		//get out the widgets
		$widgets = $this->getWidgets();

		foreach( $widgets as $widget ){

			//Ignore the page footer widgets
			if( !$widget->isDestroyed() ){
				$widget->render();
				$widget->destroyMe();				
			}
		}
		
		if(strtolower($module) != 'auth') {
			require("ui/partials/section_end.php");
			require("ui/partials/footer.php");
		}
		
		require("ui/partials/tail.php");

		if($this->scripts)
			echo $this->scripts;

		echo '</body></html>';
	}
	
	protected function includeRequiredScripts($scripts){
		
		$this->scripts = $scripts;
	}
}
?>