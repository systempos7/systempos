<?php 

	include "../../conexion.php";
	session_start();

//print_r($_POST);exit;
	 //Extraer datos del detalle_temp

	$query_conf = mysqli_query($conection,"SELECT moneda FROM configuracion");
		$result_conf = mysqli_num_rows($query_conf);
		$usuario = $_SESSION['idUser'];


				if ($result_conf > 0) {
					$info_conf = mysqli_fetch_assoc($query_conf);
					$moned = $info_conf['moneda'];
				}

			$por_pagina = $_POST['cantidad'];

			//Buscador en tirmpo real
			if (isset($_POST['busqueda'])) {
					$busqueda = mysqli_escape_string($conection,$_POST['busqueda']);

					$sql_registe = mysqli_query($conection,"SELECT COUNT(*) as total_registro FROM caja 
																WHERE (
																		fecha LIKE '%$busqueda%') ");
			$result_register = mysqli_fetch_array($sql_registe);
			$total_registro = $result_register['total_registro'];

			if(empty($_POST['pagina']))
			{
				$pagina = 1;
			}else{
				$pagina = $_POST['pagina'];
			}

			$desde = ($pagina-1) * $por_pagina;
			$total_pagina = ceil($total_registro / $por_pagina);

				$query = mysqli_query($conection,"SELECT c.fecha,c.inicio,c.ventas,c.abonos,c.creditos,c.egresos,c.total_efectivo,c.status,u.nombre as usuario 
													FROM caja c
													INNER JOIN usuario u
													ON c.usuario = u.idusuario 
													WHERE (c.fecha LIKE '%$busqueda%') 
											 		ORDER BY c.id DESC 
											 		LIMIT $desde,$por_pagina ");


				}
				
				$result = mysqli_num_rows($query);
				$lista = '';
				$detalleTabla = '';
				$arrayData    = array();

				$detalleTabla.='
								<table>
									<tr>
										<th>Fecha</th>
										<th>Inicio</th>
										<th>Ventas</th>
										<th>Abonos</th>
										<th>Créditos</th>
										<th>Egresos</th>
										<th>Total efectivo</th>
										<th>Usuario</th>
										<th>Estado</th>

									</tr>';

				if ($result > 0) {
				  
				while ($data = mysqli_fetch_assoc($query)){
					if ($data['status'] == 1){
							$estatus = '<span class="pagada">Abierta</span>';
						}else{
							$estatus = '<span class="anulada">Cerrada</span>';
						}
					
					$detalleTabla .= '<tr>
						                <td>'.$data['fecha'].'</td>
						                <td class="">'.$moned.' '.number_format($data['inicio'],2).'</td>
						                <td class="">'.$moned.' '.number_format($data['ventas'],2).'</td>
						                <td class="">'.$moned.' '.number_format($data['abonos'],2).'</td>
						                <td class="">'.$moned.' '.number_format($data['creditos'],2).'</td>
						                <td class="">'.$moned.' '.number_format($data['egresos'],2).'</td>
						                <td class="">'.$moned.' '.number_format($data['total_efectivo'],2).'</td>
						                <td class="">'.$data['usuario'].'</td>
						                <td class="">'.$estatus.'</td>';
								         
				}
				$detalleTabla.='</table>';

				$lista.='<ul>';

				if ($pagina > 1) {
					$lista.= '<li><a href="1"><i class="fas fa-step-backward"></i></a></li>
				<li><a href="'.($pagina-1).'"><i class="fas fa-caret-left"></i></a></li>';
				}

			//muestro de los enlaces 
			//cantidad de link hacia atras y adelante
 			$cant = 2;
 			//inicio de donde se va a mostrar los links
			$pagInicio = ($pagina > $cant) ? ($pagina - $cant) : 1;
			//condicion en la cual establecemos el fin de los links
			if ($total_pagina > $cant)
			{
				//conocer los links que hay entre el seleccionado y el final
				$pagRestantes = $total_pagina - $pagina;
				//defino el fin de los links
				$pagFin = ($pagRestantes > $cant) ? ($pagina + $cant) :$total_pagina;
			}
			else 
			{
				$pagFin = $total_pagina;
			}

				for ($i=$pagInicio; $i <= $pagFin; $i++) 
				{ 

						if ($i == $pagina) 
						{
							$lista.= '<li class="pageSelected">'.$i.'</a></li>';	
						}else{
							$lista.= '<li><a href="'.$i.'">'.$i.'</a></li>';
						}
					}

				if ($pagina < $pagFin) {
					$lista.= '<li><a href="'.($pagina+1).'"><i class="fas fa-caret-right"></i></a></li>
				<li><a href="'.($total_pagina).'"><i class="fas fa-step-forward"></i></a></li>';
				}
				$lista.='</ul>';

				$arrayData['detalle'] = $detalleTabla;
				$arrayData['totales'] = $lista;

				echo json_encode($arrayData,JSON_UNESCAPED_UNICODE);	               
			}else{
				echo 'error';
			}
			mysqli_close($conection);
		
		exit;
	   

			

	?>