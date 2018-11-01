<div id="alertasPpales" class="modalAlerts" style="background-color: rgba(0, 0, 0, 0.4); display: none;">

<?php 
    $size = getimagesize($detalle->ImagenId);
    $height = $size[1]; 
    $width = $size[0]; 
    
    print_r($size);
    
    echo($width);
    echo '<br/>';  
    echo($height);  
?>

@if(isset($detalle->Orientacion) && $detalle->Orientacion == 'h' && $width > $height || isset($detalle->Orientacion) && $width > $height)    
	<div id="imagenAlerta" class="modal-content imagenAlertaHorizontal">  
		<span class="closeModal" style="top: -30px !important; right: -20px !important;"><img src="https://png.icons8.com/office/40/000000/cancel.png" height="40"></span>
@else
	<div id="imagenAlerta" class="modal-content imagenAlertaVertical" style="max-width: 373px; !important">
		<span class="closeModal" style="top: -30px !important; right: -15px !important;"><img src="https://png.icons8.com/office/40/000000/cancel.png" height="40"></span>
@endif
    @if(!empty($detalle->HiperVinculo))
    	<a href="{{$detalle->HiperVinculo}}" target="_blank">
	@else
    	<a>
	@endif
    	<img class="" 
         id="" 
         src="{{$detalle->ImagenId}}">
     </a>
    </div>
    <div id="captionModal"></div>
</div>

