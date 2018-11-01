{{ $msjSuccess = $msjWarning = $msjError = $msjInfo = null }}

<!-- MENSAJES DE ERROR -->
@if(session()->has('ErrorMessages'))
    <?php $msjError .= '<ul style="text-align: center; list-style: none">'; ?>
    @foreach(session()->get('ErrorMessages') as $msg)
        <?php
        $msjError .= '<li>' . $msg . '</li>';        
        ?>
    @endforeach
    <?php $msjError .= '</ul>'; ?>
@endif

<!-- MENSAJES DE ADVERTENCIA -->
@if(session()->has('WarningMessages'))
    <?php $msjWarning .= '<ul style="text-align: center; list-style: none">'; ?>
    @foreach(session()->get('WarningMessages') as $msg)
        <?php
        $msjWarning .= '<li>' . $msg . '</li>';        
        ?>
    @endforeach
    <?php $msjWarning .= '</ul>'; ?>
@endif

<!-- MENSAJES DE EXITO -->
@if(session()->has('SuccessMessages'))
    <?php $msjSuccess /*.= '<ul style="text-align: justify; list-style: none">'*/; ?>
    @foreach(session()->get('SuccessMessages') as $msg)
        <?php
        $msjSuccess .= /*'<li>' .*/ $msg /*. '</li>'*/;     
        ?>
    @endforeach
    <?php //$msjSuccess .= '</ul>'; ?>
@endif

<!-- MENSAJES INFO -->
@if(session()->has('InfoMessages'))
    <?php //$msjInfo .= '<ul style="text-align: center; list-style: none">'; ?>
    @foreach(session()->get('InfoMessages') as $msg)
        <?php
        $msjInfo .= $msg;     
        ?>
    @endforeach
    <?php //$msjInfo .= '</ul>'; ?>
@endif

<!-- PINTAR MENSAJES-->
@if(!is_null($msjError) || !is_null($msjWarning) || !is_null($msjSuccess) || !is_null($msjInfo))
    @section('messageScripts')    
        jQuery(document).ready(function () {
            @if(session()->has('ErrorMessages'))
                showError('error', '<?=$msjError;?>');
            @endif
            @if(session()->has('WarningMessages'))
                showError('warning', '<?=$msjWarning;?>');
            @endif
            @if(session()->has('SuccessMessages'))
                showError('success', '<?=$msjSuccess;?>');
            @endif            
            @if(session()->has('InfoMessages'))
                showError('info', '<?=$msjInfo;?>');
            @endif            
        });
    @endsection
@endif