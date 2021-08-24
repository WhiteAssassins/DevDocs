$(function(){
    if($.cookie('modo') == 0 || $.cookie('modo') == null){
        $('body').removeClass('modooscuro')
    }else{
        $('body').addClass('modooscuro')
    }
    $(document.body).on('click','.btn_modal_login',function(){
        $('#modal_login').modal()
    })
    $(document.body).on('click','.btn_modal_reg',function(){
        $('#modal_reg').modal()
    })
    $(document.body).on('click','.btn_modal_pedido',function(){
        $('#modal_pedido').modal()
    })
    $(document.body).on('click','.btn_modal_peticiones',function(){
        $('#modal_peticiones').modal()
    })
    $(document.body).on('click','.btn_modal_add',function(){
        $('#modal_add').modal()
    })
    $(document.body).on('click','.btn_modal_docs',function(){
        $('#modal_docs').modal()
    })
    $(document.body).on('click','.btn_modal_verqos',function(){
        $('#modal_verqos').modal()
    })
    $(document.body).on('click','.btn_modal_enviarqos',function(){
        $('#modal_enviarqos').modal()
    })
    
    $(document.body).on('click','.modo_btn',function(){
        if($.cookie('modo') == 0 || !$.cookie('modo')){
            //activa el modo oscuro
            $.cookie('modo',1)
            $('body').addClass('modooscuro')
        }else{
            //desactiva el modo oscuro
            $.cookie('modo',0)
            $('body').removeClass('modooscuro')
        }
    })
    $(document.body).on("submit", "#form_upload_test", function(e) {
        e.preventDefault();
        var o = new FormData(document.getElementById("form_upload_test"));
        $.ajax({
            type: "POST",
            url: config.base_url + "admin/upload",
            data: o,
            cache: !1,
            contentType: !1,
            processData: !1,
            dataType: "json",
            success: function(e) {
                if(e.status == 200){
                    //exito
                    toastr.success('Documentacion Agregada.');
                }else{
                    //error y e.sms es el error
                    toastr.error(e.sms);
                }
                
            },
            error: function() {
                toastr.error('Error revise su coneccion a internet');
            }
        }),
        !1
    })
    $(document.body).on("submit", "#form_peticion", function(e) {
        e.preventDefault();
        var o = new FormData(document.getElementById("form_peticion"));
        $.ajax({
            type: "POST",
            url: config.base_url + "home/pedido",
            data: o,
            cache: !1,
            contentType: !1,
            processData: !1,
            dataType: "json",
            success: function(e) {
                if(e.status == 200){
                    //exito
                    toastr.success('Pedido Enviado.');
                }else{
                    //error y e.sms es el error
                    toastr.error(e.sms);
                }
                
            },
            error: function() {
                toastr.error('Error Llene todos los Campos');
            }
        }),
        !1
    })
    $(document.body).on("submit", "#form_qos", function(e) {
        e.preventDefault();
        var o = new FormData(document.getElementById("form_qos"));
        $.ajax({
            type: "POST",
            url: config.base_url + "home/qos",
            data: o,
            cache: !1,
            contentType: !1,
            processData: !1,
            dataType: "json",
            success: function(e) {
                if(e.status == 200){
                    //exito
                    toastr.success('Queja o Sugerencia Enviada.');
                }else{
                    //error y e.sms es el error
                    toastr.error(e.sms);
                }
                
            },
            error: function() {
                toastr.error('Error Llene todos los Campos');
            }
        }),
        !1
    })

    
})
