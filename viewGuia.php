<!DOCTYPE html>
<html>
<head>
	<title></title>
	<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
</head>
<body>
    <script type="text/javascript">
        $(document).ready(function(){
			let dataOrder = getParameterByName('dataOrder');
			setTimeout(function(){
			    viewGuia(dataOrder)
			},500)
    	})
        
    	function getParameterByName(name) {
		    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
		    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
		    results = regex.exec(location.search);
		    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
		}
		
		function viewGuia(numOrden){
		    let datosGuias = new Object();
				datosGuias.numPedido = numOrden;		        
			let datosGuiasJson = JSON.stringify(datosGuias);
			
			$.post("../EstafetaController.php",
				{
				  action:"getDataGuias",
			      parametros:datosGuiasJson,
				},
				function(data,textStatus) {	           
					let source = '../guiasPDF/'+data[0].guiaPdf;
                    let a = document.createElement('a');
                    a.href= source;
                    a.click();
				},
				"json"
			);
		}
		
    </script>
</body>
</html>