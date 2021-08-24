
<nav class="mb-1 navbar navbar-expand-lg navbar-dark info-color">
    <a class="navbar-brand animate__animated animate__bounceIn" href="<?php echo base_url(''); ?>">DevDocs</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent-333"
        aria-controls="navbarSupportedContent-333" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent-333">
        <ul class="navbar-nav mr-auto">
            <!--li class="nav-item">
                <a class="nav-link animate__animated animate__bounceIn" href="http://netlab.freedom.snet/">Foro Netlab</a>
            </li>
            <li class="nav-item">
                <a class="nav-link animate__animated animate__bounceIn" href="http://gitlab.freedom.snet/">Gitlab</a>
            </li>
            <li class="nav-item">
                <a class="nav-link animate__animated animate__bounceIn" href="http://chat-netlab.freedom.snet/">Matterlab</a>
            </li>
            <li class="nav-item">
                <a class="nav-link animate__animated animate__bounceIn" href="ftp://netlab:netlab@ftpnetlab.freedom.snet/">FtpNetlab</a>
            </li>
            <li class="nav-item">
                <a class="nav-link animate__animated animate__bounceIn" href="http://freelink.freedom.snet/">Freelink</a>
            </li-->
        </ul>
        <ul class="navbar-nav ml-auto nav-flex-icons animate__animated animate__bounceIn">
            <form class="form-inline my-2 my-lg-0 ml-auto" method="post" action="<?php echo base_url('home/buscar'); ?>">
                <input class="form-control" type="search" placeholder="Buscar" aria-label="Buscar" id="buscar" name="buscar">
                
            </form>
            <li class="nav-item">
                
                <a class="nav-link waves-effect waves-light modo_btn animate__animated animate__bounceIn">
                <i class="fa fa-moon-o animate__animated animate__bounceIn"></i> Modo Oscuro
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link waves-effect waves-light btn_modal_pedido animate__animated animate__bounceIn">
                <i class="fa fa-check animate__animated animate__bounceIn"></i> Hacer pedido
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link waves-effect waves-light btn_modal_enviarqos animate__animated animate__bounceIn">
                <i class="fa fa-bug animate__animated animate__bounceIn"></i> Enviar Queja o Sugerencia
                </a>
            </li>
            <!--li class="nav-item">
                <a class="nav-link waves-effect waves-light animate__animated animate__bounceIn" href="<?php echo base_url()?>changelog">
                <i class="fa fa-info animate__animated animate__bounceIn"></i> Changelogs
                </a>
                
            </li-->
            <?php
                if(!$this->session->userdata('username')){
            ?>
            <li class="nav-item">
                    <a class="nav-link waves-effect btn_modal_reg waves-light animate__animated animate__swing">
                    <i class="fa fa-user animate__animated animate__swing"></i> Registro
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link waves-effect btn_modal_login waves-light animate__animated animate__swing">
                    <i class="fa fa-user animate__animated animate__swing"></i> Login
                    </a>
                </li>
            <?php
                }else{
            ?>
            
               
                </li>
                <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" id="navbarDropdownMenuLink" data-toggle="dropdown"
          aria-haspopup="true" aria-expanded="false"><i class="fa fa-user"></i> <?php echo $this->session->userdata('username'); ?></a>
        <div class="dropdown-menu dropdown-info dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
          <a class="dropdown-item" href="<?php echo base_url('admin'); ?>"><i class="fa fa-cogs"></i> Panel Administrativo</a>
          <a class="dropdown-item" href="<?php echo base_url('home/logout'); ?>"><i class="fa fa-sign"></i>Desconectarse</a>
        </div>
      </li>
                
                
                
            <?php
                }
            ?>
        </ul>
    </div>
</nav>

<?php
    if(isset($sms) && $tipo == 'error'){
        echo '<div class="alert alert-info text-center">'.$sms.'</div>';
    };
    if(isset($error)){
        echo '<div class="alert alert-info text-center">'.$error.'</div>';
    };
?>