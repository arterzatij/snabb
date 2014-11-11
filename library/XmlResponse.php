<?php

class XmlResponse extends Response {

  protected $xmlRoot;
  protected $vars = array();

  public function __construct( $xmlRoot, $vars = array() ) {

    $this->xmlRoot = $xmlRoot;
    $this->vars = $vars;

  }

  public function execute() {

    header("Content-type:application/xml");

    $xmlName = Inflector::camel( $this->xmlRoot );
    
    $vars = $this->vars;

    $xml = new SimpleXMLElement("<$xmlName/>");

    $this->array_to_xml( $vars, $xml );

    print $xml->asXML();

  }

  protected function array_to_xml( $info, &$xml ) {

    foreach( $info as $key => $value ) {

      if( is_array($value) ) {

        if( !is_numeric($key) ) {

          $subnode = $xml->addChild("$key");
          array_to_xml( $value, $subnode );

        } else {

          $subnode = $xml->addChild("item$key");
          array_to_xml( $value, $subnode );

        }
      } else {

        $xml->addChild( "$key", htmlspecialchars("$value") );

      }
    }
  }

}
?>