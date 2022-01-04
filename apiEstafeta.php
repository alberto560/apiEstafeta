<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'libs/PHPMailer/src/Exception.php';
require 'libs/PHPMailer/src/PHPMailer.php';
require 'libs/PHPMailer/src/SMTP.php';

    class estafeta extends BD{
        function getDataOrder($parametros)
        {          
          $sentencia = $this->ConsultaPreparada("SELECT
                      p.ID as order_id,
                      p.post_date,
                        max( CASE WHEN pm.meta_key = '_billing_email' and p.ID = pm.post_id THEN pm.meta_value END ) as billing_email,
                        max( CASE WHEN pm.meta_key = '_billing_phone' and p.ID = pm.post_id THEN pm.meta_value END ) as billing_phone,
                        max( CASE WHEN pm.meta_key = '_billing_first_name' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_first_name,
                        max( CASE WHEN pm.meta_key = '_billing_last_name' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_last_name,
                        max( CASE WHEN pm.meta_key = '_billing_address_1' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_address_1,
                        max( CASE WHEN pm.meta_key = '_billing_address_2' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_address_2,
                        max( CASE WHEN pm.meta_key = '_shipping_colonia' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_colonia,
                        max( CASE WHEN pm.meta_key = '_billing_city' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_city,
                        max( CASE WHEN pm.meta_key = '_billing_state' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_state,
                        max( CASE WHEN pm.meta_key = '_billing_postcode' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_postcode,
                        max( CASE WHEN pm.meta_key = '_shipping_first_name' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_first_name,
                        max( CASE WHEN pm.meta_key = '_shipping_last_name' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_last_name,
                        max( CASE WHEN pm.meta_key = '_shipping_address_1' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_address_1,
                        max( CASE WHEN pm.meta_key = '_shipping_address_2' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_address_2,
                        max( CASE WHEN pm.meta_key = '_shipping_city' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_city,
                        max( CASE WHEN pm.meta_key = '_shipping_state' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_state,
                        max( CASE WHEN pm.meta_key = '_shipping_postcode' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_postcode,
                        max( CASE WHEN pm.meta_key = '_order_total' and p.ID = pm.post_id THEN pm.meta_value END ) as order_total,
                        max( CASE WHEN pm.meta_key = '_order_tax' and p.ID = pm.post_id THEN pm.meta_value END ) as order_tax,                        
                        max( CASE WHEN pm.meta_key = '_paid_date' and p.ID = pm.post_id THEN pm.meta_value END ) as paid_date,
                        
                        (SELECT GROUP_CONCAT(oim.meta_value SEPARATOR '|') FROM wp_woocommerce_order_items oi join wp_woocommerce_order_itemmeta oim on oi.order_item_id = oim.order_item_id WHERE oi.order_id = ? and oi.order_item_type = 'line_item' and oim.meta_key = '_qty') AS articulos,
                        
                        (SELECT GROUP_CONCAT(oim.meta_value SEPARATOR '|') FROM wp_woocommerce_order_items oi join wp_woocommerce_order_itemmeta oim on oi.order_item_id = oim.order_item_id WHERE oi.order_id = ? and oi.order_item_type = 'line_item' and oim.meta_key = '_line_total') AS costoComodin,
                        
                        (SELECT GROUP_CONCAT(oim.meta_value SEPARATOR '|') FROM wp_woocommerce_order_items oi join wp_woocommerce_order_itemmeta oim on oi.order_item_id = oim.order_item_id WHERE oi.order_id = ? and oi.order_item_type = 'line_item' and oim.meta_key = '_product_id') AS id_productos,
                        
                        ( select group_concat( order_item_name separator '|' ) from wp_woocommerce_order_items where order_id = p.ID and order_item_type = 'line_item') as order_items
                  from
                      wp_posts p 
                      join wp_postmeta pm on p.ID = pm.post_id
                      join wp_woocommerce_order_items oi on p.ID = oi.order_id 
                      join wp_woocommerce_order_itemmeta oim on oi.order_item_id = oim.order_item_id
                  where
                      post_type = 'shop_order' and p.ID = ?
                  group by
                      p.ID", array($parametros->orderNum,$parametros->orderNum,$parametros->orderNum,$parametros->orderNum));
          return json_encode($sentencia);
          
        }
        
        
        function getDataProducto($parametros){
            /*
            $sentencia2 = $this->ConsultaPreparada("SELECT ID FROM wp_posts WHERE post_title = ? limit 1 ", array($parametros->name));
            $data = array_shift($sentencia2);
            $dataa = json_encode($data);
            $d = json_decode($dataa);
            $idProducto = $d->ID;
            */
            $sentencia = $this->ConsultaPreparada("SELECT post_id, 
                            max(case when meta_key = '_weight' then meta_value end) as weight, 
		                    max(case when meta_key = '_length' then meta_value end) as lenght, 
                            max(case when meta_key = '_width' then meta_value end) as widht, 
                            max(case when meta_key = '_height' then meta_value end) as height 
                            from wp_postmeta where post_id = ? 
                            group by post_id order by post_id", array($parametros->idProd));
            return json_encode($sentencia);
        }


        function apiEstafeta($parametros)
        {

          $numPedido = implode(array($parametros->numPedido));
          
          $contenido_del_envio = implode(array($parametros->contenido_del_envio));
          $forma_de_entrega = implode(array($parametros->forma_de_entrega));
          $numero_de_etiquetas = implode(array($parametros->numero_de_etiquetas));
          $numero_de_oficina = implode(array($parametros->numero_de_oficina));
          $codigo_postal_destino = implode(array($parametros->codigo_postal_destino));
          $tipo_de_envio = implode(array($parametros->tipo_de_envio));
          $tipo_de_servicio = implode(array($parametros->tipo_de_servicio));
          $peso_del_envio = implode(array($parametros->peso_del_envio));
          $tipo_de_papel = implode(array($parametros->tipo_de_papel));

          $informacion_adicional_del_envio = implode(array($parametros->informacion_adicional_del_envio));
          $descripcion_del_contenido = implode(array($parametros->descripcion_del_contenido));
          $centro_de_costos = implode(array($parametros->centro_de_costos));
          $pais_de_envio = implode(array($parametros->pais_de_envio));
          $referencia = implode(array($parametros->referencia));          
          $cuadrante_de_impresion = implode(array($parametros->cuadrante_de_impresion));          

          $direccion_destinatario = implode(array($parametros->direccion_destinatario));
          $direccion_destinatarioDos = implode(array($parametros->direccion_destinatarioDos));
          $colonia_destinatario = implode(array($parametros->colonia_destinatario));
          $ciudad_destinatario = implode(array($parametros->ciudad_destinatario));
          $codigo_postal_destinatario = implode(array($parametros->codigo_postal_destinatario));
          $estado_destinatario = implode(array($parametros->estado_destinatario));
          $contacto_destinatario = implode(array($parametros->contacto_destinatario));
          $razon_social_destinatario = implode(array($parametros->razon_social_destinatario));
          $numero_cliente_destinatario = implode(array($parametros->numero_cliente_destinatario));
          $celular_destinatario = implode(array($parametros->celular_destinatario));
          $telefono_destinatario = implode(array($parametros->telefono_destinatario));
          
          
         /*
          $ESTAFETA_CREATE_LABEL_URL = 'https://labelqa.estafeta.com/EstafetaLabel20/services/EstafetaLabelWS?wsdl';
          $ESTAFETA_CREATE_LABEL_CUSTNUM = '0000000';
          $ESTAFETA_CREATE_LABEL_USER = 'prueba1';
          $ESTAFETA_CREATE_LABEL_PASS = 'lAbeL_K_11';
          $ESTAFETA_CREATE_LABEL_ID = 28;
          */
          
          $ESTAFETA_CREATE_LABEL_URL = 'https://label.estafeta.com/EstafetaLabel20/services/EstafetaLabelWS?wsdl';
          $ESTAFETA_CREATE_LABEL_CUSTNUM = '5513140';
          $ESTAFETA_CREATE_LABEL_USER = '5513140';
          $ESTAFETA_CREATE_LABEL_PASS = 'PAmqVdV2o';
          $ESTAFETA_CREATE_LABEL_ID = 'MR';
          
          $i = (Object) [

          'content'                  => $contenido_del_envio, /* Contenido del envío Char(1 a 25) (SI) */
          'deliveryToEstafetaOffice' => $forma_de_entrega,/* Si es “True”, el envío es “Entrega Ocurre” es decir se entregará en una oficina Estafeta en lugar del domicilio del destinatario (bolean) (SI)*/
          'numberOfLabels'           => $numero_de_etiquetas, /* Número de etiquetas que se desean imprimir con el tipo de servicio min:1 mx:70 (SI)*/
          'officeNum'                => $numero_de_oficina, /* Número de Oficina Estafeta string (000 a 999) (SI)*/
          'originZipCodeForRouting'  => $codigo_postal_destino, /* Código postal del domicilio destino del envío Char(5) Decimal(5) (SI)*/
          'parcelTypeId'             => $tipo_de_envio, /* Tipo de envío int(1 - sobre, 4 - paquete) (SI)*/
          'serviceTypeId'            => $tipo_de_servicio, /* Identificador de tipo de Servicio Estafeta para la impresión de guías Char(2) (SI)*//*Consultar lista de servicios con su asesor de ventas (SI)*/
          'valid'                    => 'true', /* (SI)*/
          'weight'                   => $peso_del_envio, /* Peso del envío Float (0.5 a 99.00) (SI)*/
          'paperType'                => $tipo_de_papel,
          /*Tipo de papel para impresión de la guía.
              1 - Papel Bond Tamaño Carta
              En esta modalidad la cara de la hoja es
              dividida en 2 secciones, en una de ellas se
              imprime la guía y en la otra se imprime el
              contrato de la guía.
              Requiere impresora Láser.
              (Ver Anexo C)

              2 - Papel Etiqueta Térmica de 6 x 4 pulgadas
              En esta modalidad la guía se imprime en la
              Etiqueta térmica (no se imprime contrato de
              la guía)
              Requiere impresora Térmica.
              (Ver Anexo B)

              3 - Plantilla Tamaño Oficio de 4 Etiquetas
              En esta modalidad la plantilla está dividida
              en 4 cuadrantes donde cada uno es una*/

          //ESTOS TODOS EMNOS RETURN-------
          'aditionalInfo'        => ($informacion_adicional_del_envio ?? '.'), /* Información adicional sobre el envío Char(1 a 25) (NO) */
          'contentDescription'   => ($descripcion_del_contenido ?? '.'),        /* Descripcion del contenido del envío Char(100) (NO) */
          'costCenter'           => ($centro_de_costos ?? '1'),      /* Centro de Costos del cliente al que pertenece el envío Char(1 a 10) (NO) */
          'destinationCountryId' => ($pais_de_envio ?? 'MX'), /* País del envío, solo se requiere definir en caso de que el envío sea hacia el extranjero (EU -Estados Unidos) (NO)*/
          'reference'            => ($referencia ?? '.'), /* Texto que sirve como referencia adicional para que Estafeta ubique mas fácilmente el domicilio destino Char(1 a 25) (NO)*/
          'returnDocument'       => 'false', /* Campo que indica si el envío requiere la impresión de una guía adicional para el manejo de documento de retorno (NO)*/

          'quadrant'             => ($cuadrante_de_impresion ?? '0'), /* Cuadrante de inicio de impresión de guías. 1-4 – impresora láser. Solo aplica cuando paperType sea 3. (1,2,3,4)*/
          //******************************

          //  Persona a quien va dirigido el envio
          'Cliente_address1'       => $direccion_destinatario, /* Línea 1 de Dirección Char(1 a 30) (SI)*/
          'Cliente_neighborhood'   => $colonia_destinatario, /* Colonia Char(1 a 50) (SI)*/
          'Cliente_city'           => $ciudad_destinatario, /* Ciudad Char(1 a 50) (SI)*/
          'Cliente_zipCode'        => $codigo_postal_destinatario, /*Código Postal Char(5) Decimal(5) (SI)*/
          'Cliente_state'          => $estado_destinatario, /* Estado Char(1 a 50) (SI)*/
          'Cliente_contactName'    => $contacto_destinatario /* Nombre de la persona de Contacto Char(1 a 30) (SI)*/,
          'Cliente_corporateName'  => $razon_social_destinatario, /* Razón social Char(1 a 50) (SI)*/
          'Cliente_customerNumber' => $numero_cliente_destinatario, /* Número de Cliente Estafeta. Puede tratarse del Número de Cliente origen o destino Char(7) (SI)*/

          //ESTOS 3
          'Cliente_address2'       => $direccion_destinatarioDos, /* Línea 2 de Dirección Char(1 a 30) (NO)*/
          'Cliente_cellPhone'      => ($celular_destinatario ?? '.'), /* Número de celular de la persona de contacto Char(0 a 20) (NO)*/
          'Cliente_phoneNumber'    => ($telefono_destinatario ?? '.'), /* Teléfono Char(5 a 25) (NO)*/
          //*************************
          'Cliente_valid'          => 'true',



          //Objeto que contiene la información del quien envia
          'Tecno_address1'       => "AV HORACIO",
          'Tecno_city'           => "MIGUEL HIDALGO",
          'Tecno_contactName'    => "NORBERTO SOLIS",
          'Tecno_corporateName'  => "HOKINS",
          'Tecno_customerNumber' => "5513140",//5513140
          'Tecno_neighborhood'   => "POLANCO IV SECCION",
          'Tecno_state'          => "CIUDAD DE MEXICO (DF)",
          'Tecno_zipCode'        => "11550",

          'Tecno_address2'       => "E:701 I:PB Y PLANTA ALTA",
          'Tecno_cellPhone'      => "5579081345",
          'Tecno_phoneNumber'    => " ",
          'Tecno_valid'          => 'true',

                                              ];


                  $xml = '<soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:est="http://estafetalabel.webservices.estafeta.com">
                       <soapenv:Header/>
                       <soapenv:Body>
                          <est:createLabel soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
                             <in0 xsi:type="dto:EstafetaLabelRequest" xmlns:dto="http://dto.estafetalabel.webservices.estafeta.com">
                                <customerNumber xsi:type="xsd:string">'.$ESTAFETA_CREATE_LABEL_CUSTNUM.'</customerNumber>
                                <!--1 or more repetitions:-->
                                <labelDescriptionList xsi:type="dto:LabelDescriptionList">

                                   <aditionalInfo xsi:type="xsd:string">'.$i->aditionalInfo.'</aditionalInfo>
                                   <content xsi:type="xsd:string">'.$i->content.'</content>
                                   <contentDescription xsi:type="xsd:string">'.$i->contentDescription.'</contentDescription>
                                   <costCenter xsi:type="xsd:string">'.$i->costCenter.'</costCenter>
                                   <deliveryToEstafetaOffice xsi:type="xsd:boolean">'.$i->deliveryToEstafetaOffice.'</deliveryToEstafetaOffice>
                                   <destinationCountryId xsi:type="xsd:string">'.$i->destinationCountryId.'</destinationCountryId>
                                   <destinationInfo xsi:type="dto:DestinationInfo">
                                      <address1 xsi:type="xsd:string">'.$i->Cliente_address1.'</address1>
                                      <address2 xsi:type="xsd:string">'.$i->Cliente_address2.'</address2>
                                      <cellPhone xsi:type="xsd:string">'.$i->Cliente_cellPhone.'</cellPhone>
                                      <city xsi:type="xsd:string">'.$i->Cliente_city.'</city>
                                      <contactName xsi:type="xsd:string">'.$i->Cliente_contactName.'</contactName>
                                      <corporateName xsi:type="xsd:string">C'.$i->Cliente_corporateName.'</corporateName>
                                      <customerNumber xsi:type="xsd:string">'.$i->Cliente_customerNumber.'</customerNumber>
                                      <neighborhood xsi:type="xsd:string">'.$i->Cliente_neighborhood.'</neighborhood>
                                      <phoneNumber xsi:type="xsd:string">'.$i->Cliente_phoneNumber.'</phoneNumber>
                                      <state xsi:type="xsd:string">'.$i->Cliente_state.'</state>
                                      <valid xsi:type="xsd:boolean">'.$i->Cliente_valid.'</valid>
                                      <zipCode xsi:type="xsd:string">'.$i->Cliente_zipCode.'</zipCode>
                                   </destinationInfo>
                                   <numberOfLabels xsi:type="xsd:int">'.$i->numberOfLabels.'</numberOfLabels>
                                   <officeNum xsi:type="xsd:string">'.$i->officeNum.'</officeNum>
                                   <originInfo xsi:type="dto:OriginInfo">
                                      <address1 xsi:type="xsd:string">'.$i->Tecno_address1.'</address1>
                                      <address2 xsi:type="xsd:string">'.$i->Tecno_address2.'</address2>
                                      <cellPhone xsi:type="xsd:string">'.$i->Tecno_cellPhone.'</cellPhone>
                                      <city xsi:type="xsd:string">'.$i->Tecno_city.'</city>
                                      <contactName xsi:type="xsd:string">'.$i->Tecno_contactName.'</contactName>
                                      <corporateName xsi:type="xsd:string">'.$i->Tecno_corporateName.'</corporateName>
                                      <customerNumber xsi:type="xsd:string">'.$i->Tecno_customerNumber.'</customerNumber>
                                      <neighborhood xsi:type="xsd:string">'.$i->Tecno_neighborhood.'</neighborhood>
                                      <phoneNumber xsi:type="xsd:string">'.$i->Tecno_phoneNumber.'</phoneNumber>
                                      <state xsi:type="xsd:string">'.$i->Tecno_state.'</state>
                                      <valid xsi:type="xsd:boolean">'.$i->Tecno_valid.'</valid>
                                      <zipCode xsi:type="xsd:string">'.$i->Tecno_zipCode.'</zipCode>
                                   </originInfo>
                                   <originZipCodeForRouting xsi:type="xsd:string">'.$i->originZipCodeForRouting.'</originZipCodeForRouting>
                                   <parcelTypeId xsi:type="xsd:int">'.$i->parcelTypeId.'</parcelTypeId>
                                   <reference xsi:type="xsd:string">'.$i->reference.'</reference>
                                   <returnDocument xsi:type="xsd:boolean">'.$i->returnDocument.'</returnDocument>
                                   <serviceTypeId xsi:type="xsd:string">'.$i->serviceTypeId.'</serviceTypeId>
                                   <valid xsi:type="xsd:boolean">True</valid>
                                   <weight xsi:type="xsd:float">'.$i->weight.'</weight>
                                </labelDescriptionList>
                                <labelDescriptionListCount xsi:type="xsd:int">1</labelDescriptionListCount>
                                <login xsi:type="xsd:string">'.$ESTAFETA_CREATE_LABEL_USER.'</login>
                                <paperType xsi:type="xsd:int">'.$i->paperType.'</paperType>
                                <password xsi:type="xsd:string">'.$ESTAFETA_CREATE_LABEL_PASS.'</password>
                                <quadrant xsi:type="xsd:int">'.$i->quadrant.'</quadrant>
                                <suscriberId xsi:type="xsd:string">'.$ESTAFETA_CREATE_LABEL_ID.'</suscriberId>
                                <valid xsi:type="xsd:boolean">'.$i->valid.'</valid>
                             </in0>
                          </est:createLabel>
                       </soapenv:Body>
                    </soapenv:Envelope>';
                    
                  $headers = array(
                                "Content-type: text/xml;charset=\"utf-8\"",
                                "Accept: text/xml",
                                "Cache-Control: no-cache",
                                "SOAPAction:http://tempuri.org/createLabel",
                                "Pragma: no-cache",
                                "Content-length: ".strlen($xml),
                            );                                
                                  
                  // PHP cURL
                  try {
                      $ch = curl_init();
                  } catch (Exception $e) {
                      echo 'Excepción capturada: ',  $e->getMessage(), "\n";
                  }  
                  
                  try{
                    
                      curl_setopt_array($ch, Array(
                          CURLOPT_URL            => $ESTAFETA_CREATE_LABEL_URL,
                          CURLOPT_POST           => true,
                          CURLOPT_POSTFIELDS     => $xml,
                          CURLOPT_HTTPHEADER     => $headers,
                          CURLOPT_SSL_VERIFYHOST => false,
                          CURLOPT_SSL_VERIFYPEER => false,
                          CURLOPT_RETURNTRANSFER => TRUE,
                          CURLOPT_ENCODING       => 'UTF-8'
                      ));
                      
                  }catch(Exception $e){
                      echo 'Excepción capturada: ',  $e->getMessage(), "\n";
                  }

                  try {
                      $response = curl_exec($ch);
                      //echo $response;
                  } catch (Exception $e) {
                      echo 'Excepción capturada: ',  $e->getMessage(), "\n";
                  }                  
                  
                  $error    = curl_error($ch);                  
                  curl_close($ch);

                  libxml_use_internal_errors(true);
                  $sxe = simplexml_load_string($response);
                  
                  if ($sxe === false) {
                      echo "Failed loading XML\n";
                      foreach(libxml_get_errors() as $error) {
                          echo "\t", $error->message;
                      }
                  }
                  
                  $xml_obj = self::xml_to_array($response, 1, 'resultDescription')['soapenv:Envelope']['soapenv:Body']['multiRef'];

                  $labelPDF = $xml_obj[0]['labelPDF']['value'];

                  foreach ($xml_obj as $key => $i) {
                      if($key > 0){
                          if($i['resultDescription']['value'] != 'OK'){
                              $numero_de_guia = $i['resultDescription']['value'];                  
                          }
                      }
                  }

                  $nombre_del_PDF = $numPedido.'-'.$numero_de_guia.".pdf";                    
                  
                  $pdf_decoded = base64_decode($labelPDF,true);
                  try{
                    $pdf = fopen('../apiEstafeta/guiasPDF/'.$nombre_del_PDF,'w') or die("Unable to open file!");

                  }catch (Exception $e) {
                      echo 'Excepción capturada: ',  $e->getMessage(), "\n";
                  }                   
                  fwrite ($pdf,$pdf_decoded);
                  fclose ($pdf);        
                  chmod('../apiEstafeta/guiasPDF/'.$nombre_del_PDF, 0777);
                  return json_encode($nombre_del_PDF);;
                  //$array_data = json_encode($xml);
                  //return $array_data;
        }        
        
        function sendGuiaMail($parametros){
            $email = implode(array($parametros->email));
			$guia = implode(array($parametros->numGuia));

			$mail = new PHPMailer(true);
			//$mensaje = 'Este es tu número de guía '.$guia;
			$mensaje = '
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>Hokins</title>
	</head>
	<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="padding: 0;">
		<div id="wrapper" dir="ltr" style="background-color: #f7f7f7; margin: 0; padding: 70px 0; width: 100%; -webkit-text-size-adjust: none;">
			<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
				<tr>
					<td align="center" valign="top">
						<div id="template_header_image">
							<p style="margin-top: 0;"><img src="https://hokins.com/wp-content/uploads/2020/11/hokins-logo.png" alt="Hokins" style="border: none; display: inline-block; font-size: 14px; font-weight: bold; height: auto; outline: none; text-decoration: none; text-transform: capitalize; vertical-align: middle; max-width: 100%; margin-left: 0; margin-right: 0;"></p>						</div>
						<table border="0" cellpadding="0" cellspacing="0" width="600" id="template_container" style="background-color: #ffffff; border: 1px solid #dedede; box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1); border-radius: 3px;">
							<tr>
								<td align="center" valign="top">
									<!-- Header -->
									<table border="0" cellpadding="0" cellspacing="0" width="100%" id="template_header" style="background-color: #382596; color: #ffffff; border-bottom: 0; font-weight: bold; line-height: 100%; vertical-align: middle; font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif; border-radius: 3px 3px 0 0;">
										<tr>
											<td id="header_wrapper" style="padding: 36px 48px; display: block;">
											    <center>
												<h1 style="font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif; font-size: 30px; font-weight: 300; line-height: 150%; margin: 0; text-align: left; text-shadow: 0 1px 0 #6051ab; color: #ffffff; background-color: inherit;">Tu pedido se encuentra en camino.</h1>
												</center>
											</td>
										</tr>
									</table>
									<!-- End Header -->
								</td>
							</tr>
							<tr>
								<td align="center" valign="top">
									<!-- Body -->
									<table border="0" cellpadding="0" cellspacing="0" width="600" id="template_body">
										<tr>
											<td valign="top" id="body_content" style="background-color: #ffffff;">
												<!-- Content -->
												<table border="0" cellpadding="20" cellspacing="0" width="100%">
													<tr>
														<td valign="top" style="padding: 48px 48px 32px;">
															<div id="body_content_inner" style="color: #636363; font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif; font-size: 14px; line-height: 150%; text-align: left;">
<center>															
<p style="margin: 0 0 16px;">Tu pedido ya se encuentra en paqueteria solo ingresa tu número de guia para rastrearlo.</p>
<center>
<center>
<h2 style="color: #382596; display: block; font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif; font-size: 18px; font-weight: bold; line-height: 130%; margin: 0 0 18px; text-align: left;">Tu número de guía es: '.$guia.'</h2>
</center>
<center>
<p style="margin: 0 0 16px;">Ingresalo en el siguiente link para rastrearlo.</p>
</center>
<center>
<h2 style="color: #382596; display: block; font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif; font-size: 18px; font-weight: bold; line-height: 130%; margin: 0 0 18px; text-align: left;"><a class="link" href="https://hokins.com/envios/">Rastréa tu pedido aquí.</a></h2>
</center>

															</div>
														</td>
													</tr>
													<tr>													
														<td>
															<div id="body_content_inner" style="color: #636363; font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif; font-size: 14px; line-height: 150%; text-align: left;">
																<center>															
																<p style="margin: 0 0 16px;">Nota: Los datos de rastreo pueden tardar unas horas en visualizarse.</p>
																<center>																
															</div>														
														</td>
													</tr>
												</table>
												<!-- End Content -->
											</td>
										</tr>
									</table>
									<!-- End Body -->
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td align="center" valign="top">
						<!-- Footer -->
						<table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer">
							<tr>
								<td valign="top" style="padding: 0; border-radius: 6px;">
									<table border="0" cellpadding="10" cellspacing="0" width="100%">
										<tr>
											<td colspan="2" valign="middle" id="credit" style="border-radius: 6px; border: 0; color: #8a8a8a; font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif; font-size: 12px; line-height: 150%; text-align: center; padding: 24px 0;">
											    <center>
												<p style="margin: 0 0 16px;">Hokins — 2021</p>
												</center>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						<!-- End Footer -->
					</td>
				</tr>
			</table>
		</div>
	</body>
</html>';
                    
            $asunto = 'Envío de Numero de Guía';
				
			try {
			    //Server settings
			    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
				$mail->isSMTP();                                            // Send using SMTP
				$mail->Host       = 'mail.hokins.com';                    // Set the SMTP server to send through
				$mail->SMTPAuth   = true;                                   // Enable SMTP authentication
				$mail->Username   = 'dev@hokins.com';                     // SMTP username
				$mail->Password   = 'devHokins21';                               // SMTP password
				$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
				$mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above 

				//Recipients
				$mail->setFrom('pedidos@hokins.com');
				$mail->addAddress($email);               // Name is optional

				// Content
				$mail->isHTML(true); // Set email format to HTML
				$mail->CharSet = 'UTF-8';
				$mail->Subject = $asunto;
				$mail->Body = $mensaje;
				$mail->send();

				echo 'Correo enviado';
				return true;
				}catch (Exception $e) {
				    echo "Correo no enviado. Mailer Error: {$mail->ErrorInfo}";
				    return 0;
				}
        }
        
        public static function xml_to_array($contents, $get_attributes=1, $priority = 'tag') {
            if(!$contents) return array();

            if(!function_exists('xml_parser_create')) {
                //print "'xml_parser_create()' function not found!";
                return array();
            }
            //Get the XML parser of PHP - PHP must have this module for the parser to work
            $parser = xml_parser_create('');
            xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
            xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
            xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
            xml_parse_into_struct($parser, trim($contents), $xml_values);
            xml_parser_free($parser);

            if(!$xml_values) return;//Hmm...

            //Initializations
            $xml_array = array();
            $parents = array();
            $opened_tags = array();
            $arr = array();

            $current = &$xml_array; //Refference

            //Go through the tags.
            $repeated_tag_index = array();//Multiple tags with same name will be turned into an array
            foreach($xml_values as $data) {
                unset($attributes,$value);//Remove existing values, or there will be trouble

                //This command will extract these variables into the foreach scope
                // tag(string), type(string), level(int), attributes(array).
                extract($data);//We could use the array by itself, but this cooler.

                $result = array();
                $attributes_data = array();

                if(isset($value)) {
                    if($priority == 'tag') $result = $value;
                    else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
                }

                //Set the attributes too.
                if(isset($attributes) and $get_attributes) {
                    foreach($attributes as $attr => $val) {
                        if($priority == 'tag') $attributes_data[$attr] = $val;
                        else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
                    }
                }

                //See tag status and do the needed.
                if($type == "open") {//The starting of the tag '<tag>'
                    $parent[$level-1] = &$current;
                    if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
                        $current[$tag] = $result;
                        if($attributes_data) $current[$tag. '_attr'] = $attributes_data;
                        $repeated_tag_index[$tag.'_'.$level] = 1;

                        $current = &$current[$tag];

                    } else { //There was another element with the same tag name

                        if(isset($current[$tag][0])) {//If there is a 0th element it is already an array
                            $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
                            $repeated_tag_index[$tag.'_'.$level]++;
                        } else {//This section will make the value an array if multiple tags with the same name appear together
                            $current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array
                            $repeated_tag_index[$tag.'_'.$level] = 2;

                            if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
                                $current[$tag]['0_attr'] = $current[$tag.'_attr'];
                                unset($current[$tag.'_attr']);
                            }

                        }
                        $last_item_index = $repeated_tag_index[$tag.'_'.$level]-1;
                        $current = &$current[$tag][$last_item_index];
                    }

                } elseif($type == "complete") { //Tags that ends in 1 line '<tag />'
                    //See if the key is already taken.
                    if(!isset($current[$tag])) { //New Key
                        $current[$tag] = $result;
                        $repeated_tag_index[$tag.'_'.$level] = 1;
                        if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data;

                    } else { //If taken, put all things inside a list(array)
                        if(isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array...

                            // ...push the new element into that array.
                            $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;

                            if($priority == 'tag' and $get_attributes and $attributes_data) {
                                $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
                            }
                            $repeated_tag_index[$tag.'_'.$level]++;

                        } else { //If it is not an array...
                            $current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
                            $repeated_tag_index[$tag.'_'.$level] = 1;
                            if($priority == 'tag' and $get_attributes) {
                                if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well

                                    $current[$tag]['0_attr'] = $current[$tag.'_attr'];
                                    unset($current[$tag.'_attr']);
                                }

                                if($attributes_data) {
                                    $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
                                }
                            }
                            $repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken
                        }
                    }

                } elseif($type == 'close') { //End of tag '</tag>'
                    $current = &$parent[$level-1];
                }
            }

            return($xml_array);
        }
        
        //========== RASTREO DE GUIAS
        function rastrearGuia($parametros){
            $numeroGuia = implode(array($parametros->numeroGuia));

            $client = new SoapClient('https://tracking.estafeta.com/Service.asmx?wsdl');
            // Arreglo de guías a consultar
            $waylbills = array();
            $waylbills[0] = $numeroGuia;
            // Se llena Objeto WaybillRange
            $WaybillRange = new StdClass();
            $WaybillRange -> initialWaybill = '';
            $WaybillRange -> finalWaybill = '';
            // Se llena objeto WaybillList, se trata guías de 22 dígitos
            $WaybillList = new StdClass();
            $WaybillList -> waybillType = 'G';
            $WaybillList -> waybills = $waylbills;
            // Se llena objeto SearchType, se indica que se trata de una lista de guías
            $SearchType = new StdClass();
            $SearchType -> waybillRange = $WaybillRange;
            $SearchType -> waybillList = $WaybillList;
            $SearchType -> type = 'L';
            // Se llena objeto HistoryConfiguration, se indica que se requiere toda la historia de las guías
            $HistoryConfiguration = new StdClass;
            $HistoryConfiguration -> includeHistory = 1;
            $HistoryConfiguration -> historyType = 'ALL';
            // Se llena objeto Filter, se indica que no se requiere el filtro por estado actual de las guías
            $Filter = new StdClass;
            $Filter -> filterInformation = 0;
            $Filter -> filterType = 'DELIVERED';
            // Se llena objeto SearchConfiguration, se indican parámetros adicionales a la búsqueda
            $SearchConfiguration = new StdClass();
            $SearchConfiguration -> includeDimensions = 0;
            $SearchConfiguration -> includeWaybillReplaceData = 0;
            $SearchConfiguration -> includeReturnDocumentData = 0;
            $SearchConfiguration -> includeMultipleServiceData = 0;
            $SearchConfiguration -> includeInternationalData = 0;
            $SearchConfiguration -> includeSignature = 0;
            $SearchConfiguration -> includeCustomerInfo = 1;
            $SearchConfiguration -> historyConfiguration = $HistoryConfiguration;
            $SearchConfiguration -> filterType= $Filter;
            
            // Se instancía al método del web service para consulta de guías
            $result = $client->ExecuteQuery(array(
              'suscriberId'=>397,
              'login'=>'5513140',
              'password'=> 'PAmqVdV2o',
              'searchType' => $SearchType,
              'searchConfiguration' => $SearchConfiguration
              )
            );
            //Se imprime resultado obtenido de la consulta al ws
            //print_r ($result);
            return json_encode($result);
        }
        
        //===================================================================================
        //===================================================================================
        function saveDataGuia($parametros){
            $sentencia = $this->InsertarRegistrosPreparada("INSERT INTO guiasGeneradas (numPedido,guiaPdf) VALUES (?,?)", array($parametros->numPedido,$parametros->nombrePdf));
            return json_encode($sentencia);
        }
        
        function getDataGuias($parametros){
            $sentencia = $this->ConsultaPreparada("SELECT guiaPdf from guiasGeneradas WHERE numPedido=?", array($parametros->numPedido));
            return json_encode($sentencia);
        }
        
        
        
        /*
SELECT
                      p.ID as order_id,
                      p.post_date,
                        max( CASE WHEN pm.meta_key = '_billing_email' and p.ID = pm.post_id THEN pm.meta_value END ) as billing_email,
                        max( CASE WHEN pm.meta_key = '_billing_phone' and p.ID = pm.post_id THEN pm.meta_value END ) as billing_phone,
                        max( CASE WHEN pm.meta_key = '_billing_first_name' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_first_name,
                        max( CASE WHEN pm.meta_key = '_billing_last_name' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_last_name,
                        max( CASE WHEN pm.meta_key = '_billing_address_1' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_address_1,
                        max( CASE WHEN pm.meta_key = '_billing_address_2' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_address_2,
                        max( CASE WHEN pm.meta_key = '_shipping_colonia' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_colonia,
                        max( CASE WHEN pm.meta_key = '_billing_city' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_city,
                        max( CASE WHEN pm.meta_key = '_billing_state' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_state,
                        max( CASE WHEN pm.meta_key = '_billing_postcode' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_postcode,
                        max( CASE WHEN pm.meta_key = '_shipping_first_name' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_first_name,
                        max( CASE WHEN pm.meta_key = '_shipping_last_name' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_last_name,
                        max( CASE WHEN pm.meta_key = '_shipping_address_1' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_address_1,
                        max( CASE WHEN pm.meta_key = '_shipping_address_2' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_address_2,
                        max( CASE WHEN pm.meta_key = '_shipping_city' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_city,
                        max( CASE WHEN pm.meta_key = '_shipping_state' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_state,
                        max( CASE WHEN pm.meta_key = '_shipping_postcode' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_postcode,
                        max( CASE WHEN pm.meta_key = '_order_total' and p.ID = pm.post_id THEN pm.meta_value END ) as order_total,
                        max( CASE WHEN pm.meta_key = '_order_tax' and p.ID = pm.post_id THEN pm.meta_value END ) as order_tax,
                        max( CASE WHEN pm.meta_key = '_paid_date' and p.ID = pm.post_id THEN pm.meta_value END ) as paid_date,
                        ( select group_concat( order_item_name separator '|' ) from wp_woocommerce_order_items where order_id = p.ID and order_item_type = 'line_item') as order_items
                  from
                      wp_posts p 
                      join wp_postmeta pm on p.ID = pm.post_id
                      join wp_woocommerce_order_items oi on p.ID = oi.order_id
                  where
                      post_type = 'shop_order' and p.ID = ?
                  group by
                      p.ID
                      */
                      
                      
                /* 
                
                SELECT
                      p.ID as order_id,
                      p.post_date,
                        max( CASE WHEN pm.meta_key = '_billing_email' and p.ID = pm.post_id THEN pm.meta_value END ) as billing_email,
                        max( CASE WHEN pm.meta_key = '_billing_phone' and p.ID = pm.post_id THEN pm.meta_value END ) as billing_phone,
                        max( CASE WHEN pm.meta_key = '_billing_first_name' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_first_name,
                        max( CASE WHEN pm.meta_key = '_billing_last_name' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_last_name,
                        max( CASE WHEN pm.meta_key = '_billing_address_1' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_address_1,
                        max( CASE WHEN pm.meta_key = '_billing_address_2' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_address_2,
                        max( CASE WHEN pm.meta_key = '_shipping_colonia' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_colonia,
                        max( CASE WHEN pm.meta_key = '_billing_city' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_city,
                        max( CASE WHEN pm.meta_key = '_billing_state' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_state,
                        max( CASE WHEN pm.meta_key = '_billing_postcode' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_postcode,
                        max( CASE WHEN pm.meta_key = '_shipping_first_name' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_first_name,
                        max( CASE WHEN pm.meta_key = '_shipping_last_name' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_last_name,
                        max( CASE WHEN pm.meta_key = '_shipping_address_1' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_address_1,
                        max( CASE WHEN pm.meta_key = '_shipping_address_2' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_address_2,
                        max( CASE WHEN pm.meta_key = '_shipping_city' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_city,
                        max( CASE WHEN pm.meta_key = '_shipping_state' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_state,
                        max( CASE WHEN pm.meta_key = '_shipping_postcode' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_postcode,
                        max( CASE WHEN pm.meta_key = '_order_total' and p.ID = pm.post_id THEN pm.meta_value END ) as order_total,
                        max( CASE WHEN pm.meta_key = '_order_tax' and p.ID = pm.post_id THEN pm.meta_value END ) as order_tax,
                        max( CASE WHEN pm.meta_key = '_paid_date' and p.ID = pm.post_id THEN pm.meta_value END ) as paid_date,
                        max( CASE WHEN oim.meta_key = 'articulos' and oi.order_item_id = oim.order_item_id THEN oim.meta_value END ) as articulos,
                        ( select group_concat( order_item_name separator '|' ) from wp_woocommerce_order_items where order_id = p.ID and order_item_type = 'line_item') as order_items
                  from
                      wp_posts p 
                      join wp_postmeta pm on p.ID = pm.post_id
                      join wp_woocommerce_order_items oi on p.ID = oi.order_id 
                      join wp_woocommerce_order_itemmeta oim on oi.order_item_id = oim.order_item_id
                  where
                      post_type = 'shop_order' and p.ID = ?
                  group by
                      p.ID
                */
                
        
        
   
  }
?>


















