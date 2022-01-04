<!DOCTYPE html>
<html>
<head>
	<title></title>
	<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
</head>
<body>
	
    <script type="text/javascript">
    	var numOrder;
    	var removeItemFromArr = ( arr, item ) => {
            var i = arr.indexOf( item );
            i !== -1 && arr.splice( i, 1 );
        };
    	
    	$(document).ready(function(){
			let dataOrder = getParameterByName('dataOrder');
			numOrder = dataOrder;
			apiEstafeta(dataOrder)
    	})

    	function getParameterByName(name) {
		    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
		    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
		    results = regex.exec(location.search);
		    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
		}

        let dataPedido
		function apiEstafeta(dataOrder) {	
			var dataGuiaEstafeta = new Object();
			    dataGuiaEstafeta.orderNum = dataOrder;

			var dataGuiaEstafetaJson = JSON.stringify(dataGuiaEstafeta);

			$.post('../EstafetaController.php',
			        {
			          action:'getDataOrder',
			          parametros:dataGuiaEstafetaJson,
			        },
			    	function(data,textStatus) {
			    	    console.log(data)
			    	    dataPedido = data
			    	    getDataWeight(data);
			    	    
			        	setTimeout(function(){
			        		generarGuia(data)
			        	},5000)
			        	
			   		 },
			    "json"
			);
			
		}
        //******************************************************
        //******************************************************
        function getDataWeight(jsonData){
            let idProds = jsonData[0].id_productos.split('|');
            let arrayWeight = [];
            
            for(let q=0;q<idProds.length;q++){
                setTimeout(function(){
                    var datos;
                
                    let idProd = idProds[q];
                    console.log(idProd)
                    
                    var dataProducto = new Object();
        			    dataProducto.idProd = idProd;
        
        			var dataProductoJson = JSON.stringify(dataProducto);
        
        			$.post('../EstafetaController.php',
        			        {
        			          action:'getDataProducto',
        			          parametros:dataProductoJson,
        			        },
        			    	function(data,textStatus) {
        			    	    datos = data;
        			    	    setTimeout(function(){
        			    	        let w = datos[0].weight
            			    	    console.log(w)
                		            arrayWeight.push(w)
        			    	    },750)
        			   		 },
        			    "json"
        			);
                },1000)
    			
            }
            
            setTimeout(function(){
                console.log(arrayWeight)
                calculateWeight(arrayWeight)
            },4000)
        }
        //******************************************************
        //******************************************************
        let pesoPaquete = 0;
        function calculateWeight(data){
            let totalWeight = 0;
            let auxCant = dataPedido[0].articulos.split('|')
            let auxCosto = dataPedido[0].costoComodin.split('|')
            console.log(auxCosto)
            let cantidades = []
            let totalPiezas = 0;
            
            
            for(let q=0;q<auxCant.length;q++){
                let cant = auxCant[q]
                cantidades.push(cant)
                totalPiezas = totalPiezas + parseInt(cant)
            }
            console.log(cantidades)
            for(let q=0;q<data.length;q++){
                if(auxCosto[q] != '0'){
                    let peso;
                    let weight = parseFloat(data[q]);
                    let cant = parseInt(cantidades[q])
                    peso = weight * cant
                    totalWeight = totalWeight + peso;
                }
            }
            console.log(totalWeight);
            
            if(totalWeight < 1){
                pesoPaquete = 1;
            }else{
                pesoPaquete = totalWeight;
            }
            //totalPiezas
            if(totalPiezas <= 30 && pesoPaquete > 1){
                pesoPaquete = pesoPaquete + .5
            }else{
                if(totalPiezas >= 31 && totalPiezas <= 50 ){
                    pesoPaquete = pesoPaquete + 1.2
                }else{
                    if(totalPiezas >=51){
                        pesoPaquete = pesoPaquete + 2
                    }
                }
            }
            
            
        }
        
        console.log(pesoPaquete)
        //******************************************************
        //******************************************************

		function generarGuia(jsonData){
		        let email = jsonData[0].billing_email;
		        
				let contenido_del_envio = 'Paquete' //arreglar //arreglado

    			let forma_de_entrega = 'false'
    			let numero_de_etiquetas = '1'
    			let numero_de_oficina = '504'//504

    			let codigo_postal_destino = jsonData[0]._shipping_postcode

    			let tipo_de_envio = '4';
    			let tipo_de_servicio = '70'
    			let pesoPack = pesoPaquete.toFixed(2)
    			let peso_del_envio = pesoPack.toString() 
    			let tipo_de_papel = '1'
    			let informacion_adicional_del_envio = '.'
                
                let arrayOrders = jsonData[0].order_items.split('|');
                let arrayDescripcion = [];
                for(let q=0;q<arrayOrders.length - 1;q++){
                    let aux = arrayOrders[q];
                    let words = aux.split(' ');
                    let descAux;
                    switch(words.length) {
                      case 2:
                           arrayDescripcion.push(aux);
                        break;
                      case 3:
                           arrayDescripcion.push(aux);
                        break;
                      case 4:
                            if(words[words.length -1] == 'piezas'){
                                words[3] = 'pzs';
                                descAux = words.toString();
                                descAux = descAux.replace(/,/g, ' ');
                                arrayDescripcion.push(descAux);
                            }else{
                                words.shift();
                                descAux = words.toString();
                                descAux = descAux.replace(/,/g, ' ');
                                arrayDescripcion.push(descAux);
                            }
                        break;
                      case 5:
                           words.shift();
                           if(words[words.length -1] == 'estampado'){
                               words[words.length -1] = 'est.'
                           }else{
                               if(words[words.length -1] == 'colores'){
                                   words[words.length -1] = 'cols.'
                               }else{
                                    if(words[words.length -1] == '3colores'){
                                       words[words.length -1] = '3cols'
                                   }else{
                                       if(words[words.length -1] == 'naranja'){
                                           words[words.length -1] = 'nja.'
                                       }else{
                                           if(words[words.length -1] == 'Negro'){
                                               words[words.length -1] = 'ngo'
                                           }
                                       }
                                   }
                               }
                           }
                           descAux = words.toString();
                           descAux = descAux.replace(/,/g, ' ');
                           arrayDescripcion.push(descAux);
                        break;
                        
                      case 6:
                           words.shift();
                           if(words[words.length -1] == 'estampado'){
                               words[words.length -1] = 'est.'
                           }
                           if(words[0] == 'de'){
                               words[3] = 'pzs'
                               words.shift();
                           }
                           descAux = words.toString();
                           descAux = descAux.replace(/,/g, ' ');
                           arrayDescripcion.push(descAux);
                        break;
                      case 8:
                           words.shift();
                           removeItemFromArr(words,'y')
                           words[3] = 'ngo'
                           words[4] = 'nja'
                           descAux = words.toString();
                           descAux = descAux.replace(/,/g, ' ');
                           arrayDescripcion.push(descAux);
                        break;
                      default:
                    }
                }
                
    			let descripcion_del_contenido = arrayDescripcion

    			let centro_de_costos = '000' 
    			let pais_de_envio = 'MX'
    			let referencia = numOrder
    			let cuadrante_de_impresion = '1'
                
                
                let direccion_destinatario;
                let direccion_destinatarioDos;
                let direccion = jsonData[0]._shipping_address_1;
                if(direccion.length > 30){
                    let auxWords = direccion.split(' ');
                    for(let q=0;q<auxWords.length;q++){
                        let word = auxWords[q];
                        if(word.charAt(word.length - 1) == '.' || word.charAt(word.length - 1) == ','){
                            word = word.substring(0,word.length-1)
                        }
                        if(word == 'fraccionamiento' || word == 'Fraccionamiento'){
                            auxWords[q] = 'Fracc';
                        }
                        if(word == 'Interior' || word == 'interior'){
                            auxWords[q] = 'Int';
                        }
                        if(word == 'Privada' || word == 'privada'){
                            auxWords[q] = 'Priv';
                        }
                        if(word == 'Seccion' || word == 'seccion' || word == 'Sección' || word == 'sección'){
                            auxWords[q] = 'Sec';
                        }
                        if(word == 'Delegacion' || word == 'delegacion'){
                            auxWords[q] = 'Del';
                        }
                    }

                    
                    let words = Math.trunc(auxWords.length / 2);

                    
                    let direccionUno = [];
                    let direccionDos = [];
                    for(let q=0;q<words;q++){
                        let word = auxWords[q];
                        if(word.charAt(word.length - 1) == '.' || word.charAt(word.length - 1) == ','){
                            console.log('es igual')
                            word = word.substring(0,word.length-1)
                            console.log(word)
                        }
                        if(word == 'fraccionamiento' || word == 'Fraccionamiento'){
                            auxWords[q] = 'Fracc';
                        }
                        if(word == 'Interior' || word == 'interior'){
                            auxWords[q] = 'Int';
                        }
                        if(word == 'Privada' || word == 'privada'){
                            auxWords[q] = 'Priv';
                        }
                        if(word == 'Seccion' || word == 'seccion' || word == 'Sección' || word == 'sección'){
                            auxWords[q] = 'Sec';
                        }
                        direccionUno.push(word);
                    }
                    direccion_destinatario = direccionUno.toString();
                    direccion_destinatario = direccion_destinatario.replace(/,/g, ' '); 
                    
                    for(let q=words;q<auxWords.length;q++){
                        let word = auxWords[q];
                        if(word.charAt(word.length - 1) == '.' || word.charAt(word.length - 1) == ','){
                            console.log('es igual')
                            word = word.substring(0,word.length-1)
                            console.log(word)
                        }
                        if(word == 'fraccionamiento' || word == 'Fraccionamiento'){
                            auxWords[q] = 'Fracc';
                        }
                        if(word == 'Interior' || word == 'interior'){
                            auxWords[q] = 'Int';
                        }
                        if(word == 'Privada' || word == 'privada'){
                            auxWords[q] = 'Priv';
                        }
                        if(word == 'Seccion' || word == 'seccion' || word == 'Sección' || word == 'sección'){
                            auxWords[q] = 'Sec';
                        }
                        
                        if(word == 'Delegacion' || word == 'delegacion'){
                            auxWords[q] = 'Del';
                        }
                        direccionDos.push(word);
                    }
                    
                    direccion_destinatarioDos = direccionDos.toString();
                    direccion_destinatarioDos = direccion_destinatarioDos.replace(/,/g, ' '); 
                    
                }else{
                    direccion_destinatario = jsonData[0]._shipping_address_1;   
                    direccion_destinatarioDos = '.'   
                }
    			
    			let direccionOne;
    			let direccionTwo;
    			if(direccion_destinatario.length > 30){
    			    direccionOne = direccion_destinatario.substr(0,29)
    			}else{
    			    direccionOne = direccion_destinatario;
    			}
    			
    			if(direccion_destinatarioDos.length > 30){
    			    direccionTwo = direccion_destinatarioDos.substr(0,29)
    			}else{
    			    direccionTwo = direccion_destinatarioDos;
    			}

                //****************************************
                let colonia = jsonData[0]._shipping_colonia; 
                let colonia_destinatario;
                if(colonia == null){
                    colonia_destinatario = '.';   
                }else{
                    colonia_destinatario = colonia;
                }
                //****************************************
    			
                let ciudad_destinatario;
    			let auxCiudad_destinatario = jsonData[0]._shipping_city;
    			if(auxCiudad_destinatario.length > 50){
    			    ciudad_destinatario = auxCiudad_destinatario.substr(0,49)
    			}else{
    			    ciudad_destinatario = jsonData[0]._shipping_city;
    			}
    			
    			let codigo_postal_destinatario = jsonData[0]._shipping_postcode
    			let estado_destinatario = jsonData[0]._shipping_state
    			let nombres = jsonData[0]._shipping_first_name.split(' ')
    			let apellidos = jsonData[0]._shipping_last_name.split(' ')
    			
    			let arrayNombre = []
    			if(nombres.length>1){
    			    if (nombres.length%2==0){
    			        let fin = nombres.length / 2
    			        for(let q=0;q<fin;q++){
    			            arrayNombre.push(nombres[q])        
    			        }
    			    }
    			}else{
    			    arrayNombre = nombres
    			}
    			
    			let arrayApellidos = []
    			if(apellidos.length>1){
    			    if (apellidos.length%2==0){
    			        let fin = apellidos.length / 2
    			        for(let q=0;q<fin;q++){
    			            arrayApellidos.push(apellidos[q])        
    			        }
    			    }
    			}else{
    			    arrayApellidos = apellidos
    			}
    			
    			let nombreCompr = arrayNombre.toString() +' '+arrayApellidos.toString()
    			
    			if(nombreCompr.length > 30){
    			    nombreCompr = nombreCompr.substr(0,29)
    			}
    			
    			let contacto_destinatario = nombreCompr
    			
    			

    			let razon_social_destinatario = jsonData[0]._shipping_first_name+' '+jsonData[0]._shipping_last_name
                
    			let numero_cliente_destinatario = jsonData[0].billing_phone.substr(3)//arreglar 

    			let celular_destinatario = jsonData[0].billing_phone
    			let telefono_destinatario = jsonData[0].billing_phone
    			
	        	let dataGuiaEstafeta = new Object();
		            dataGuiaEstafeta.numPedido = numOrder;
		            dataGuiaEstafeta.contenido_del_envio = contenido_del_envio;
		            dataGuiaEstafeta.forma_de_entrega = forma_de_entrega;
		            dataGuiaEstafeta.numero_de_etiquetas = numero_de_etiquetas;
		            dataGuiaEstafeta.numero_de_oficina = numero_de_oficina;
		            dataGuiaEstafeta.codigo_postal_destino = codigo_postal_destino;
		            dataGuiaEstafeta.tipo_de_envio = tipo_de_envio;
		            dataGuiaEstafeta.tipo_de_servicio = tipo_de_servicio;
		            dataGuiaEstafeta.peso_del_envio = peso_del_envio;
		            dataGuiaEstafeta.tipo_de_papel = tipo_de_papel;
		            dataGuiaEstafeta.informacion_adicional_del_envio = informacion_adicional_del_envio;
		            dataGuiaEstafeta.descripcion_del_contenido = descripcion_del_contenido;
		            dataGuiaEstafeta.centro_de_costos = centro_de_costos;
		            dataGuiaEstafeta.pais_de_envio = pais_de_envio;
		            dataGuiaEstafeta.referencia = referencia;
		            dataGuiaEstafeta.cuadrante_de_impresion = cuadrante_de_impresion;
		            dataGuiaEstafeta.direccion_destinatario = direccionOne;
		            dataGuiaEstafeta.direccion_destinatarioDos = direccionTwo;
		            dataGuiaEstafeta.colonia_destinatario = colonia_destinatario;
		            dataGuiaEstafeta.ciudad_destinatario = ciudad_destinatario;
		            dataGuiaEstafeta.codigo_postal_destinatario = codigo_postal_destinatario;
		            dataGuiaEstafeta.estado_destinatario = estado_destinatario;
		            dataGuiaEstafeta.contacto_destinatario = contacto_destinatario;
		            dataGuiaEstafeta.razon_social_destinatario = razon_social_destinatario;
		            dataGuiaEstafeta.numero_cliente_destinatario = numero_cliente_destinatario;
		            dataGuiaEstafeta.celular_destinatario = celular_destinatario;
		            dataGuiaEstafeta.telefono_destinatario = telefono_destinatario;
		        let dataGuiaEstafetaJson = JSON.stringify(dataGuiaEstafeta);
		    			
				$.post('../EstafetaController.php',
				    {
				        action:'apiEstafeta',
				        parametros:dataGuiaEstafetaJson,
				    },
				    function(data,textStatus) {
				        saveDataPedido(data);
				        
				        setTimeout(function(){
				            sendMail(data, email);
				        },500)
				        
				        
				        
				        setTimeout(function(){
				            let source = '../guiasPDF/'+data;
                            let a = document.createElement('a');
                            a.href= source;
                            a.click();
				        },1000)
                        
				    },
				    "json"
				);			
		}
        
        
        //**********************************************************
        //**********************************************************
        function saveDataPedido(datosGuia){
            let data = datosGuia.split('-');
            let numPedido = data[0];
            
            let dataGuia = new Object();
		        dataGuia.numPedido = numPedido;
		        dataGuia.nombrePdf = datosGuia;
		        
		    let dataGuiaJson = JSON.stringify(dataGuia);
		    			
				$.post('../EstafetaController.php',
				    {
				        action:'saveDataGuia',
				        parametros:dataGuiaJson,
				    },
				    function(data,textStatus) {
                        console.log(data)                        
				    },
				    "json"
				);			
        }
        //**********************************************************
        //**********************************************************
        function sendMail(jsonData, email){
            let data = jsonData.split('-');
            let numGuiaAux = data[1].split('.');
            let numGuia = numGuiaAux[0]
            
            let dataGuiaEstafeta = new Object();
                dataGuiaEstafeta.numGuia = numGuia;
                dataGuiaEstafeta.email = email;
            let dataGuiaEstafetaJson = JSON.stringify(dataGuiaEstafeta);
		    			
				$.post('../EstafetaController.php',
				    {
				        action:'sendGuiaMail',
				        parametros:dataGuiaEstafetaJson,
				    },
				    function(data,textStatus) {
				        //console.log(data)
				    },
				    "json"
				);			
        }
        //**********************************************************
        //**********************************************************
        
		function SaveToDisk(fileURL, fileName) {
		    try{
				// for non-IE
			    if (!window.ActiveXObject) {
			        var save = document.createElement('a');
			        save.href = fileURL;
			        save.target = '_blank';
			        save.download = fileName || 'unknown';

			        var evt = new MouseEvent('click', {
			            'view': window,
			            'bubbles': true,
			            'cancelable': false
			        });
			        save.dispatchEvent(evt);

			        (window.URL || window.webkitURL).revokeObjectURL(save.href);
			    }

			    // for IE < 11
			    else if ( !! window.ActiveXObject && document.execCommand)     {
			        var _window = window.open(fileURL, '_blank');
			        _window.document.close();
			        _window.document.execCommand('SaveAs', true, fileName || fileURL)
			        _window.close();
			    }
		    }catch(error){
		    	alert(error)
		    }
		}

    </script>
</body>
</html>