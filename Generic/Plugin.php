<?php
namespace PNPSystem\Generic;

use \AIOSystem\Api\System;
use \AIOSystem\Api\Database;
use \AIOSystem\Api\Template;
use \AIOSystem\Api\Event;
use \AIOSystem\Api\Configuration;

use \MVCSystem\Library\Model;

class Plugin {
	const PLUGIN_PREFIX = 'PNPData';
	/**
	 * @var \AIOSystem\Module\Configuration\Ini|null $Configuration
	 */
	private $Configuration = null;

	protected function getModel(){
		//\MVCSystem\Generic\Model::BuildModel( $this->getNamespace(). );
		var_dump( $this->getNamespace() );
	}
	protected function getNamespace(){
		if( false === strpos( ( $Class = get_class( $this ) ), '\\' ) ) {
			return '';
		} else {
			return dirname( $Class );
		}
	}
	protected function getDirectory(){
		return System::DirectorySyntax( __DIR__.'/..'.str_replace( 'PNPSystem', '', $this->getNamespace() ), true, DIRECTORY_SEPARATOR );
	}
	protected function useSetting( $Group, $Key = null, $Value = null ){
		if( $this->Configuration === null ) {
			$this->Configuration = Configuration::Ini( $this->getDirectory().basename($this->getDirectory()).'.ini' );
		}
		return $this->Configuration->Entry( $Group, $Key, $Value );
	}
	protected function getTemplate( $File ) {
		return Template::Load( $this->getDirectory().'Template/'.$File );
	}

	protected function useDatabaseInstall() {
		$Table = $this->useSetting( 'Database' );
		foreach( (array)$Table as $Alias => $TableName ) {
			$TableDefinition = $this->useSetting( 'Database.'.$Alias );
			$DefinitionList = array();
			foreach( (array)$TableDefinition as $Field => $Definition ) {
				array_push( $DefinitionList, explode( '|', trim( $Field.'|'.$Definition ) ) );
			}
			Database::DropTable( $this->getDatabaseTable( $Alias ) );
			Database::CreateTable( $this->getDatabaseTable( $Alias ), $DefinitionList );
		}
	}
	protected function getDatabaseTable( $Table ) {
		return str_replace(' ', '', ucwords(
			preg_replace( array('![^\w\d]!is', '!\s{2,}!'), array(' ',''),
				self::PLUGIN_PREFIX.'\\'.$this->getNamespace().'\\'.$Table
			)
		));
	}

	protected function readRecordValue( $Record, $Name ) {
		if( property_exists( $Record, $Name ) ) {
			return $Record->$Name;
		} else
		if( property_exists( $Record, strtolower($Name) ) ) {
			$Name = strtolower($Name);
		} else
		if( property_exists( $Record, strtoupper($Name) ) ) {
			$Name = strtoupper($Name);
		}
		return $Record->$Name;
	}
}
?>
