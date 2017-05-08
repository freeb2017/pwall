<?php 

class WidgetFactory{
	
	private static $widget_instance = array();

	private static function getClassPath( $name_space ){
		
		$name_space = explode( '::', $name_space );
		
		$depth = count( $name_space );
		
		$w_index = $depth - 1;
		$w_class = $name_space[ $w_index ];
		
		unset( $name_space[ $w_index ] );
		
		$path = 'ui/widget/';
		$path .= implode( '/', $name_space ) . '/' . $w_class . '.php';
		
		return array( $path, $name_space, $w_class );
	}
	
	public static function getWidget( $name_space, $args = array() ){
		
		list( $path, $name_space, $w_class ) = WidgetFactory::getClassPath( $name_space );
			
		if( !is_array( $args ) ) $args = array( $args );
		
		try{

			//error_log("including the path: $path");
			include_once $path;
			
			$reflection = new ReflectionClass( $w_class );
			$instance = $reflection->newInstanceArgs( $args );
			
			array_push( WidgetFactory::$widget_instance, $instance );
			
		}catch ( Exception $e ){
			
			$msg = $e->getMessage();
			
			die( 'Not Found' . $msg );
		}

		return $instance;
	}	
	
	public static function getWidgetInstance( $name_space ){
		
		$widgets = array();
		list( $path, $name_space, $w_class ) = WidgetFactory::getClassPath( $name_space );
		
		foreach( WidgetFactory::$widget_instance as $instance ){
			
			if( file_exists( $path ) && $instance instanceof $w_class ){
				
				array_push( $widgets, $instance );
			}
		}
		
		return $widgets;
	}
}

?>
