<?php

namespace App\Http\Controllers\Pedidos;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Util\CallWebService;
use Maatwebsite\Excel\Facades\Excel;
use App\Util\Helpers;

class ExportController extends Controller {

    private $client;
    private $request;
    private $response;
    
    public function __construct() {
        //Verificamos que el usuario este autenticado
        $this->middleware('auth');
        //Creamos una nueva instancia de la clase para llamado de servicios REST
        $this->client = new CallWebService();
    }
    
    public function exportHistoryOrders(Request $request) {
        $url = '';
        
        if(Helpers::isDirector()) {
            $url = "/odata/GetOrderbyDirZona(id='" . auth()->user()->document . "',marca='" . auth()->user()->brand . "',opcion=4,idOrder=null)";
        }
        
        if(Helpers::isAdministrator()) {
            $url = "/odata/GetOrderToAdmin(marca='" . auth()->user()->brand . "',opcion=5,campanaI=null,campanaF=null,codZona=null,mailPlan=null,idOrder=null)";
        }
        
        //Llamamos el servicio ODATA para obtener el historico pedios 
        $this->request = $this->client->callGet(
            $url, 
            []
        );
        
        Excel::create('Histórico Pedidos Web', function ($excel) {
            $excel->sheet('Productos', function($sheet) {
                $sheet->row(1, [
                    'Pedido', 
                    'Fecha', 
                    'Campaña', 
                    'Documento', 
                    'Asesora', 
                    'Teléfono',
                    'Cupo Minimo', 
                    'Cupo Maximo',
                    'Zona',
                    'Mail Plan',
                    'División',
                    'Estado'
                ]);
                
                if(!empty($this->request->result) && !empty($this->request->result->value)) {
                    $status = '';
                    $index = 1;
                    foreach ($this->request->result->value as $order) {
                        if($order->Procesado == '0') {
                            $status = 'Enviado';
                        }
                        
                        if ($order->Procesado == '1') {
                            $status = 'Enviado';
                        }
                        
                        if ($order->Procesado == '2') {
                            $status = 'Guardado';
                        }
                        
                        if ($order->Procesado == '3') {
                            $status = 'Facturado';
                        }
                        
                        $index ++;
                        $sheet->row($index, [
                            $order->PedidoId,
                            $order->Fecha,
                            $order->CampanaId,
                            $order->NumeroDcto,
                            $order->NombreAsesora,
                            $order->Telefono,
                            $order->MontoMin == 0 ? 'Sí' : 'No',
                            $order->CupoMax == 0 ? 'Sí' : 'No',
                            $order->CodigoZona,
                            $order->MailPlan,
                            $order->Division,
                            $status
                        ]);
                    }
                }
                
                //Agregamos el auto filtro
                $sheet->setAutoFilter();
            });
        })->download('xlsx');
    }
    
    public function exportCurrentOrders(Request $request, $zone = null) { 
        $url = '';
        
        $zone = (!empty($zone)) ? "'" . $zone . "'" : 'null';
        
        if(Helpers::isDirector()) {
            $url = "/odata/GetOrderbyDirZona(id='" . auth()->user()->document . "',marca='" . auth()->user()->brand . "',opcion=3,idOrder=null)";
        }
        
        if(Helpers::isAdministrator()) {
            $url = "/odata/GetOrderToAdmin(marca='" . auth()->user()->brand . "',opcion=4,campanaI=null,campanaF=null,codZona=" . $zone . ",mailPlan=null,idOrder=null)";
        }
        
        //Llamamos el servicio ODATA para obtener el historico pedios
        $this->request = $this->client->callGet(
            $url,
            []
        );
        
        Excel::create('Pedidos En Curso', function ($excel) {
            $excel->sheet('Productos', function($sheet) {
                $sheet->row(1, [
                    'Pedido',
                    'Fecha',
                    'Campaña',
                    'Documento',
                    'Asesora',
                    'Teléfono',
                    'Total Factura',
                    'Cupo Minimo',
                    'Cupo Maximo',
                    'Saldo Pagar',
                    'Cupo Catalogo',
                    'Zona',
                    'Mail Plan',
                    'Clasificación',
                    'Estado Stencil',
                    'División',
                    'Estado'
                ]);
                
                if(!empty($this->request->result) && !empty($this->request->result->value)) {
                    $status = '';
                    $index = 1;
                    foreach ($this->request->result->value as $order) {
                        
                        if($order->Procesado == '0') {
                            $status = 'Enviado';
                        }
                        
                        if ($order->Procesado == '1') {  
                            $status = 'Enviado';
                        }
                        
                        if ($order->Procesado == '2') {
                            $status = 'Guardado';
                        } 
                        
                        if ($order->Procesado == '3') {
                            $status = 'Facturado';
                        }
                            
                        $index ++;
                        $sheet->row($index, [
                            $order->PedidoId,
                            $order->Fecha,
                            $order->CampanaId,
                            $order->NumeroDcto,
                            $order->NombreAsesora,
                            $order->Telefono,
                            $order->TotalFactura,
                            $order->MontoMin == 0 ? 'Sí' : 'No',
                            $order->CupoMax == 0 ? 'Sí' : 'No',
                            $order->SaldoPagar,
                            $order->CupoCatalogo,
                            $order->CodigoZona,
                            $order->MailPlan,
                            $order->ClasificacionValor,
                            $order->EstadoStencil,
                            $order->Division,
                            $status
                        ]);
                    }
                }
                
                //Agregamos el auto filtro
                $sheet->setAutoFilter();
            });  
        })->download('xlsx');
    }
    
}
