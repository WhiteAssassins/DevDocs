<div class="modal fade" id="modal_peticiones" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Peticiones</h5>
            <div class="table-responsive text-nowrap">

    

    </div>

            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">    
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Link</th>
                            <th scope="col">Idioma</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $resultado = $this->db->get('pedidos');
                            $rest = $resultado->result_array(); 
                            foreach ($rest as $key) {                    
                        ?>
                        <tr>
                        <td><?php echo $key['id']; ?></td>
                            <td><?php echo $key['nombre']; ?></td>
                            <td><button class="btn"><a href="<?php echo $key['link']; ?>">Direccion </button></a></td>
                            <td><?php echo $key['idioma']; ?></td>
                            <td><span class="badge badge-pill badge-danger"><form method="post" action="admin/delpet"><input type="hidden" name="nombre" value="<?php echo $key['nombre'];?>"><button type="submit" class="sort-red"><span class="fa fa-trash"></button></form></a></span></td>
                        </tr>
                        
                        <?php
                            }
                        ?>                    
                    </tbody>
                </table>
                
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
        </div>
        </div>
    </div>
    </div>
    <div class="modal fade" id="modal_add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h4 class="modal-title w-100 font-weight-bold">Añadir Documentación</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <div class="modal-body mx-3">
            <form action="<?php echo base_url('admin/upload'); ?>" id="form_upload_test" method="post">
                <div class="md-form mb-5">
                    <i class="fas fa-file-text prefix grey-text"></i>
                    <input type="text" id="defaultForm-email" class="form-control validate" name="nombre">
                    <label data-error="Error" data-success="Correcto" for="defaultForm-email">Nombre</label>
                </div>
                <div class="md-form mb-5">
                    <i class="fas fa-share prefix grey-text"></i>
                    <input type="text" id="defaultForm-email" class="form-control validate" name="descripcion">
                    <label data-error="Error" data-success="Correcto" for="defaultForm-email">Descripcion</label>
                    
                </div>
                <div class="md-form mb-4">

                    <div class="file-field">
                        <a class="btn-file-c btn-floating peach-gradient mt-0 float-left">
                            <i class="fas fa-paperclip" aria-hidden="true"></i>
                            <input type="file" name="img_docs">
                        </a>
                        <div class="file-path-wrapper">
                            <input class="file-path validate" type="text" placeholder="Upload your file">
                        </div>
                    </div>
                </div>
                <div class="md-form mb-4">
                    <i class="fas fa-comment prefix grey-text"></i>
                    <input type="text" id="defaultForm-pass" class="form-control validate" name="direccion">
                    <label data-error="Error" data-success="Correcto" for="defaultForm-pass">Direccion</label>
                </div>
                <div class="md-form mb-4">
                    <i class="fas fa-comment prefix grey-text"></i>
                    <input type="text" id="defaultForm-pass" class="form-control validate" name="idiomaa">
                    <label data-error="Error" data-success="Correcto" for="defaultForm-pass">Idioma</label>
                </div>
                <div class="md-form mb-4">
                    <i class="fas fa-comment prefix grey-text"></i>
                    <input type="text" id="defaultForm-pass" class="form-control validate" name="tipo">
                    <label data-error="Error" data-success="Correcto" for="defaultForm-pass">Tipo</label>
                </div>

            </div>
            <div class="modal-footer d-flex justify-content-center">
                <button class="btn btn-info" type="submit">Enviar</button>
            </div>
            </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_docs" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-docs">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Documentaciones</h5>
            <div class="table-responsive text-nowrap">

    

    </div>

            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">    
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Descripcion</th>
                            <th scope="col">Imagen</th>
                            <th scope="col">Direccion</th>
                            <th scope="col">Idioma</th>
                            <th scope="col">Tipo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $resultado = $this->db->get('docs');
                            $rest = $resultado->result_array(); 
                            foreach ($rest as $key) {                    
                        ?>
                        <tr>
                        <td><?php echo $key['id']; ?></td>
                            <td><?php echo $key['nombre']; ?></td>
                            <td><?php echo $key['descripcion']; ?>Direccion</td>
                            <td><img class="img-docs-admin" src="<?php echo base_url()?>img/<?php echo $key['imagen']; ?>"></td>
                            <td><button class="btn"><a href="docs/<?php echo $key['direccion']; ?>">Direccion </button></a></td>
                            <td><?php echo $key['idioma']; ?></td>
                            <td><?php echo $key['tipo']; ?></td>
                            <td><span class="badge badge-pill badge-danger"><form method="post" action="admin/deldoc"><input type="hidden" name="nombre" value="<?php echo $key['nombre'];?>"><button type="submit" class="sort-red"><span class="fa fa-trash"></button></form></a></span></td>
                        </tr>
                        
                        <?php
                            }
                        ?>                    
                    </tbody>
                </table>
                
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
        </div>
        </div>
    </div>
    </div>
    <div class="modal fade" id="modal_verqos" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Quejas o Sugerencias</h5>
            <div class="table-responsive text-nowrap">

    

    </div>

            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">    
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Texto</th>
                            <th scope="col">Ip</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $resultado = $this->db->get('qos');
                            $rest = $resultado->result_array(); 
                            foreach ($rest as $key) {                    
                        ?>
                        <tr>
                        <td><?php echo $key['id']; ?></td>
                            <td><?php echo $key['nombre']; ?></td>
                            <td><?php echo $key['texto']; ?></td>
                            <td><?php echo $key['ip']; ?></td>
                            <td><span class="badge badge-pill badge-danger"><form method="post" action="admin/delqos"><input type="hidden" name="id" value="<?php echo $key['id'];?>"><button type="submit" class="sort-red"><span class="fa fa-trash"></button></form></a></span></td>
                        </tr>
                        
                        <?php
                            }
                        ?>                    
                    </tbody>
                </table>
                
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
        </div>
        </div>
    </div>
    </div>
<?php 
$cantidaddoc = $this->db->get('docs');
$cantidadpedidos = $this->db->get('pedidos');
$cantidadqos = $this->db->get('qos');
$cantdoc = $cantidaddoc->num_rows();
$cantpedidos = $cantidadpedidos->num_rows();
$cantqos = $cantidadqos->num_rows();
?>


<div class="container">
    <button class=" btn btn-info waves-effect btn_modal_peticiones waves-light">Peticiones<span badge class="badge-pill badge-danger badge-admin"><?php echo $cantpedidos; ?></span></button>
    <button class=" btn btn-info waves-effect btn_modal_add waves-light">Añadir Documentaciones</button>
    <button class=" btn btn-info waves-effect btn_modal_docs waves-light">Documentaciones<span badge class="badge-pill badge-danger badge-admin"><?php echo $cantdoc; ?></span></button>
    <button class=" btn btn-info waves-effect btn_modal_verqos waves-light">Quejas o Sugerencias<span badge class="badge-pill badge-danger badge-admin"><?php echo $cantqos; ?></span></button>

    <div class="row row_dd">
        <div>
            <div class="card text-white bg-info mb-3">
                <div class="card-header"><i class="fas fa-eye"></i>Visitas de hoy</div>
            <div class="card-body">
                <?php
                   // use VisualAppeal\Matomo;
                      //  $matomo = new Matomo('http://matomo.freedom.snet/', '28766214922d1b5080a232ecbc0acea6', 2);
                       // $matomo->setFormat(Matomo::FORMAT_XML);
                       // $matomo->setPeriod(Matomo::PERIOD_DAY);
                       // $data = $matomo->getVisits();
                ?>
                <h3><?php //echo $data;?></h3>
            </div>
        </div>
        </div>
        <div>
            <div class="card text-white bg-info mb-3">
                <div class="card-header"><i class="fas fa-eye"></i>Visitas Unicas</div>
            <div class="card-body">
                <?php                  
                   // $matomo->setFormat(Matomo::FORMAT_XML);
                   // $matomo->setPeriod(Matomo::PERIOD_MONTH);
                   // $data = $matomo->getUniqueVisitors();
                ?>
                <h3><?php// echo $data;?></h3>
                </div>
            </div>
        </div>
        <div>
            <div class="card text-white bg-info mb-3">
                <div class="card-header"><i class="fas fa-eye"></i>Visitas Totales</div>
            <div class="card-body">
                <?php    
                  //  $matomo->setFormat(Matomo::FORMAT_XML);
                   // $matomo->setPeriod(Matomo::PERIOD_WEEK);
                   // $data = $matomo->getVisits();
                ?>
                <h3><?php //echo $data;?></h3>
                </div>
            </div>
        </div>
        <div>
            <div class="card text-white bg-info mb-3">
                <div class="card-header"><i class="fas fa-eye"></i>Acciones</div>
            <div class="card-body">
                <?php    
                   // $matomo->setFormat(Matomo::FORMAT_XML);
                    //$matomo->setPeriod(Matomo::PERIOD_MONTH);
                    //$data = $matomo->getActions();
                ?>
                <h3><?php //echo $data;?></h3>
                </div>
            </div>
        </div>
    </div>

</div>

