<?php 

	include "../../conexion.php";
	session_start();
		require_once '../pdf/vendor/autoload.php';
		use Dompdf\Dompdf;
	 //Extraer datos del detalle_temp
	//print_r($_POST);
		$query_conf = mysqli_query($conection,"SELECT * FROM configuracion");
		$result_conf = mysqli_num_rows($query_conf);

		if($result_conf > 0){
			$configuracion = mysqli_fetch_assoc($query_conf);
		}

			$usuario = $_SESSION['idUser'];

		if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2 ){
				//Busqueda por rango de fecha
				if (isset($_REQUEST['fecha_de']) && isset($_REQUEST['fecha_a'])){
					//print_r($_REQUEST);
					if (empty($_REQUEST['busqueda'])) {
						$busqueda = '';
					}else{
						$busqueda = $_REQUEST['busqueda'];
					}

					$fecha_de = mysqli_escape_string($conection,$_REQUEST['fecha_de']);
					$fecha_a = mysqli_escape_string($conection,$_REQUEST['fecha_a']);
					$f_de = $fecha_de.' 00:00:00';
					$f_a = $fecha_a.' 23:59:59';
					//$where = "fecha BETWEEN '$f_de' AND '$f_a'";

					$sql_registe = mysqli_query($conection,"SELECT COUNT(*) as total_registro FROM compras 
											WHERE fecha BETWEEN '{$f_de} ' AND '{$f_a}'
											AND codproveedor = $busqueda AND (status = 3 OR status = 4)");

			$result_register = mysqli_fetch_array($sql_registe);
			$total_registro = $result_register['total_registro'];

			$por_pagina = 10;

			if(empty($_POST['pagina']))
			{
				$pagina = 1;
			}else{
				$pagina = $_POST['pagina'];
			}

			$desde = ($pagina-1) * $por_pagina;
			$total_pagina = ceil($total_registro / $por_pagina);

				$query = mysqli_query($conection,"SELECT f.nocompra,f.fecha,f.totalcompra,f.codproveedor,f.status,f.abono,
															u.nombre as vendedor,
															cl.proveedor as proveedor
														FROM compras f
														INNER JOIN usuario u 
														ON f.usuario = u.idusuario
														INNER JOIN proveedor cl 
														ON f.codproveedor = cl.codproveedor
														WHERE f.fecha BETWEEN '{$f_de} ' AND '{$f_a}'
														AND f.codproveedor = $busqueda AND (f.status = 3 OR f.status = 4)
														ORDER BY f.fecha ASC LIMIT $desde,$por_pagina");
				}
			}


				//Fin de la busqueda por rango de fecha
				
				$result = mysqli_num_rows($query);

			ob_start();
		    include(dirname('__FILE__').'/reportePdfRango_mov_pagar.php');
		    $html = ob_get_clean();

			// instantiate and use the dompdf class
			$dompdf = new Dompdf();

			$dompdf->loadHtml($html);
			// (Optional) Setup the paper size and orientation
			$dompdf->setPaper('letter', 'portrait');
			// Render the HTML as PDF
			$dompdf->render();
			// Output the generated PDF to Browser
			$dompdf->stream('reportePdf.pdf',array('Attachment'=>0));
			exit;
?>