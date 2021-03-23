<!DOCTYPE html> 
@extends("MP.layouts.mpcdn")
@section("title")
Mood Provider 3 (Alpha)
@endsection

@section("headex")
<script>
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 5000,
        timerProgressBar: true,
        onOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });
</script>
<style>
    .jstree-default .jstree-clicked{
        background: #002002;
        border-radius: 2px;
        box-shadow: inset 0 0 1px #9990;
    }
    .jstree-default .jstree-hovered{
        background: #001f2f;
        border-radius: 2px;
        box-shadow: inset 0 0 1px #9990;
    }
</style>
@endsection

@section("context")
<div style="height: 100vh; width: 100vw;background-color: #181818;margin: 0px;display: flex;">
    <div style="border-right-style: groove; width: 338px;border-width: 1px;height: 100vh;background-color: #0d0d0d;">
        <div>
            <div id="filesystem" style="color: slategray;background-color: #040404;height: 100vh;overflow-y: auto;">
                <h3 class="text-warning" style="text-align: center">MoodProvider <span class="text-primary">4</span><span class="text-secondary" style="font-size: small;">.0.0.1</span></h3>
                <hr>
                <div id="tree" >
                    <ul id="filedata">
                        <!--DATA -->
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div style="width: calc(100vw - 338px); height: 100vh;background-color: #000">
        <div  style="height: 100vh;background-color: #111;border-bottom-style: groove;border-width: 1px;">
            <style>
                .nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active {
                    color: #ececec;
                    background-color: #4d7497a3;
                    border-color: #404040 #404040 #404040;
                }
                .nav-item > .nav-link{
                    color: gray;
                }
                .nav-item > .nav-link:hover{
                    border-color: gray;
                    color: #ececec;
                }
            </style>
            <ul id="mp-tab" class="nav nav-tabs" id="pills-tab" role="tablist" style="border-bottom: 1px solid #282828;">
            </ul>
            <div id="mp-tab-content" class="tab-content" id="pills-tabContent">
            </div>
        </div>
    </div>
</div>
    <script>
        $(document).ready(function(){
            Swal.fire({
              icon: 'info',
              title: 'Loading...',
              showConfirmButton: false
            });
            refreshmenu();
            //loadContents();
            
        });
        function funcionesAce(){
            document.onkeydown = function (e) {
                e = e || window.event;//Get event
                if (!e.ctrlKey)
                    return;
                var code = e.which || e.keyCode;//Get key code
                //
                var tab_id = $("#mp-tab-content").children("div .active").attr('id');
                var id = tab_id.substring(7);
                ace.require("ace/ext/language_tools");
                var currentAce = ace.edit("editor"+id);
                switch (code) {
                    case 83://Block Ctrl+S
                    case 87://Block Ctrl+W -- Not work in Chrome and new Firefox
                        e.preventDefault();
                        e.stopPropagation();
                        Toast.fire({
                            icon: 'info',
                            title: 'Guardando...',
                        });
                        var code = currentAce.getSession().getValue();
                        $.ajax({
                            type: "POST",
                            url: "/mp_save",
                            data: {'code': code, 'id': id},
                            success: function(data){
                                if(data=="MISS"){
                                    Toast.fire({
                                      icon: 'warning',
                                      title: 'Something Missed',
                                    });
                                    validate_is_login();
                                }else if(data=="OK"){
                                    $("#panel-"+id+" > a.nav-link > i.fa").remove();
                                    Toast.fire({
                                      icon: 'success',
                                      title: 'Saved',
                                    });
                                }else{
                                    Toast.fire({
                                      icon: 'danger',
                                      title: data,
                                    });
                                }
                            },
                            error:function(e){
                                Swal.fire({
                                  icon: 'error',
                                  title: 'Oops...',
                                  text: 'JSON.stringify(e)',
                                })
                            }
                        });
                    break
                }
            }
        }
        function instanceClose(){
            $(".close-ace").click(function(){
                var idAce = $(this).attr('data');
                closeAce(idAce);
            });
        }
        function closeAce(id){
            $.ajax({
                type: "GET",
                url: "/close_ace",
                data: {id:id},
                success: function (data)
                {
                    $("#nav-mp"+id).parent().remove();
                    $("#content"+id).remove();
                }
            });
            
        }
        function refreshmenu(){
            $.ajax({
                type: "POST",
                url: "/mp/api/mp_file_system",
                data: {"_token": "{{ csrf_token() }}"},
                success: function (data)
                {
                    if(data == ""){
                        //location.reload();
                    }else{
                        $("#filedata").html(data);
                        $('#tree').jstree({
                            "core" : {
                            "check_callback" : true
                            },
                            "plugins" : [ "dnd" ]
                        });
                        Toast.fire({
                          icon: 'success',
                          title: "Completed",
                        });
                        dblclickint();
                    }
                }
            });
        }
        // function loadContents(){
        //     $.ajax({
        //         type: "GET",
        //         url: "/load_tabs",
        //         success: function (data)
        //         {
        //             if(data == ""){
        //                 //location.reload();
        //             }else{
        //                 $("#mp-tab").append(data);
        //                 instanceClose();
        //                 Toast.fire({
        //                   icon: 'success',
        //                   title: "Completed",
        //                 });
        //             }
        //         }
        //     });
        //     $.ajax({
        //         type: "GET",
        //         url: "/load_contents",
        //         success: function (data)
        //         {
        //             if(data == ""){
        //                 //location.reload();
        //             }else{
        //                 $("#mp-tab-content").append(data);
        //                 var firts = 0;
        //                 $( "li").each(function( index ) {
        //                     var idcon = $(this).children().attr('href').substring(8);
        //                     ace.require("ace/ext/language_tools");
        //                     var editor = ace.edit("editor"+idcon);
        //                     editor.setTheme("ace/theme/tomorrow_night_bright");
        //                     editor.getSession().setMode("ace/mode/php");
        //                     editor.setOptions({
        //                         enableBasicAutocompletion: true,
        //                         enableSnippets: true,
        //                         enableLiveAutocompletion: true
        //                     });
        //                     if(firts == 0){
        //                         $("#nav-mp"+idcon).click();
        //                     }
        //                     funcionesAce();
        //                     firts = 1;
        //                 });
        //             }
        //         }
        //     });
        // }
        function dblclickint(){
            $('#tree').dblclick(function (e) {
                var node = $(e.target).closest("li");
                var path = node.attr('rel');
                var user = node.attr('data');
                var file = node.attr('file');
                if(node.hasClass('mp-file')){
                    if(node.hasClass('text-warning')){
                        Swal.fire({
                            icon: 'info',
                            title: 'File Used',
                        })
                    }else{
                        $.ajax({
                            type: "GET",
                            url: "/mp/api/ace_tab",
                            data: {path:path,file:file},
                            success: function (data)
                            {
                                if(data > 0){
                                    $("#nav-mp"+data).click();
                                }else{
                                    getContentMp(user,path,file);
                                    $("#mp-tab").append(data);
                                }
                            }
                        });
                    }
                }
            });
        }
    </script>
@endsection