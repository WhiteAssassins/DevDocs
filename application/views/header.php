<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>DevDocs</title>
    <link rel="stylesheet" href="<?php echo base_url('public/'); ?>fontawesome/css/fontawesome.css">
	<link rel="stylesheet" href="<?php echo base_url('public/'); ?>fontawesome/css/brands.css">
	<link rel="stylesheet" href="<?php echo base_url('public/'); ?>fontawesome/css/solid.css">
    <link rel="stylesheet" href="<?php echo base_url('public/'); ?>fontawesome/css/iconos.css">
    <link rel="stylesheet" href="<?php echo base_url('public/'); ?>css/addons-pro/timeline.css">
    <link rel="stylesheet" href="<?php echo base_url('public/'); ?>css/addons-pro/timeline.min.css">
    <link rel="stylesheet" href="<?php echo base_url('public/'); ?>css/mdb.css"> 
    <link rel="stylesheet" href="<?php echo base_url('public/'); ?>css/bootstrap.css">
    <link rel="stylesheet" href="<?php echo base_url('public/'); ?>css/animate.css">
    <link rel="stylesheet" href="<?php echo base_url('public/'); ?>css/main.css">
    <link rel="icon" type="image/x-icon" href="<?php echo base_url(''); ?>img/favicon.ico">
    <link href="<?php echo base_url('assets/addchat/css/addchat.min.css') ?>" rel="stylesheet">
<script type="text/javascript">
  var _paq = window._paq = window._paq || [];
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u="//matomo.freedom.snet/";
    _paq.push(['setTrackerUrl', u+'matomo.php']);
    _paq.push(['setSiteId', '2']);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.type='text/javascript'; g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
  })();
</script>

</head>
<body class="body2">

<div class="modal fade" id="modal_login" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header text-center">
            <h4 class="modal-title w-100 font-weight-bold">Login Admin</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body mx-3">
            <form action="<?php echo base_url('home/login'); ?>" method="post">
                <div class="md-form mb-5">
                    <i class="fas fa-user prefix grey-text"></i>
                    <input type="text" id="defaultForm-email" class="form-control validate" name="nombre">
                    <label data-error="Error" data-success="Correcto" for="defaultForm-email">Nombre</label>
                </div>

                <div class="md-form mb-4">
                    <i class="fas fa-lock prefix grey-text"></i>
                    <input type="password" id="defaultForm-pass" class="form-control validate" name="pass">
                    <label data-error="Error" data-success="Correcto" for="defaultForm-pass">Contraseña</label>
                </div>
        </div>
                <div class="modal-footer d-flex justify-content-center">
                    <button class="btn btn-info" type="submit">Login</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_reg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header text-center">
            <h4 class="modal-title w-100 font-weight-bold">Registro</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body mx-3">
            <form action="<?php echo base_url('home/reg'); ?>" method="post">
                <div class="md-form mb-5">
                    <i class="fas fa-user prefix grey-text"></i>
                    <input type="text" id="defaultForm-email" class="form-control validate" name="nombre">
                    <label data-error="Error" data-success="Correcto" for="defaultForm-email">Nombre</label>
                </div>

                <div class="md-form mb-4">
                    <i class="fas fa-lock prefix grey-text"></i>
                    <input type="password" id="defaultForm-pass" class="form-control validate" name="pass">
                    <label data-error="Error" data-success="Correcto" for="defaultForm-pass">Contraseña</label>
                </div>
                <div class="md-form mb-4">
                    <i class="fas fa-lock prefix grey-text"></i>
                    <input type="password" id="defaultForm-pass" class="form-control validate" name="pass1">
                    <label data-error="Error" data-success="Correcto" for="defaultForm-pass">Confirmar Contraseña</label>
                </div>
        </div>
                <div class="modal-footer d-flex justify-content-center">
                    <button class="btn btn-info" type="submit">Registro</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_pedido" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h4 class="modal-title w-100 font-weight-bold">Hacer Petición</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <div class="modal-body mx-3">
            <form action="<?php echo base_url('home/pedido'); ?>" method="post" id="form_peticion">
                <div class="md-form mb-5">
                    <i class="fas fa-file-text prefix grey-text"></i>
                    <input type="text" id="defaultForm-email" class="form-control validate" name="nombre">
                    <label data-error="Error" data-success="Correcto" for="defaultForm-email">Nombre de la Documentación</label>
                </div>
                <div class="md-form mb-5">
                    <i class="fas fa-share prefix grey-text"></i>
                    <input type="text" id="defaultForm-email" class="form-control validate" name="link">
                    <label data-error="Error" data-success="Correcto" for="defaultForm-email">Link de Internet</label>
                </div>

                <div class="md-form mb-4">
                    <i class="fas fa-comment prefix grey-text"></i>
                    <input type="text" id="defaultForm-pass" class="form-control validate" name="idioma">
                    <label data-error="Error" data-success="Correcto" for="defaultForm-pass">Idioma</label>
                </div>

            </div>
            <div class="modal-footer d-flex justify-content-center">
                <button class="btn btn-info" type="submit">Enviar</button>
            </div>
            </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_enviarqos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h4 class="modal-title w-100 font-weight-bold">Enviar Quejas o Sugerencias</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <div class="modal-body mx-3">
            <form action="<?php echo base_url('home/qos'); ?>" method="post" id="form_qos">
                <div class="md-form mb-5">
                    <i class="fas fa-user prefix grey-text"></i>
                    <input type="text" id="defaultForm-email" class="form-control validate" name="nombre">
                    <label data-error="Error" data-success="Correcto" for="defaultForm-email">Nombre Usuario</label>
                </div>
                <div class="md-form mb-5">
                    <i class="fas fa-comment prefix grey-text"></i>
                    <input type="text" id="defaultForm-email" class="form-control validate" name="texto">
                    <label data-error="Error" data-success="Correcto" for="defaultForm-email">Texto</label>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <button class="btn btn-info" type="submit">Enviar</button>
            </div>
            </form>
            </div>
        </div>
    </div>

    





   



    


    