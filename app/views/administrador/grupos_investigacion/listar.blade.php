@extends('plantilla')

@section('styles')
    @if(isset($styles))
        @foreach($styles as $style) 
            <link rel="stylesheet" href="/{{ $style }}" type="text/css" />
        @endforeach
    @endif
    <style type="text/css">
            input.ui-select-search{
                pointer-events:none;
            }
            
            li.current a{
                color: #fff;
                background-color: #337ab7;
                border-color: #2e6da4;
            }
            li.current a:hover{
                color: #fff;
                background-color: #286090;
                border-color: #204d74;
            }
            
            @media screen and (min-width: 992px) {
                .text-right2{
                    text-align: right;
                }
            }
        </style>
@stop <!--Stop section 'styles'-->

@section('pre_scripts')
    @if(isset($pre_scripts))
        @foreach($pre_scripts as $script) 
            <script type="text/javascript" src="/{{ $script }}"></script>
        @endforeach
    @endif
@stop <!--Stop section 'pre_scripts'-->

@section('contenido')
    
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="/"><i class="fa fa-home" style="font-size:18px;"></i><b></b></a></li> <i class="fa fa-chevron-right" aria-hidden="true"></i> <li><a href="/grupos/listar"><b>Grupos de investigación</b></a></li>
        </ol>
        <br />
    </section>
    
    <section class="content" ng-cloak>
        <div class="box" ng-controller="listar_grupos_investigacion_controller" ng-init='data.sedes={{ json_encode($sedes) }}'>
            <?php $hay_notify_operacion_previa = Session::get('notify_operacion_previa') ?>
            @if(isset($hay_notify_operacion_previa))
                <span ng-hide="true" ng-init="notify_operacion_previa={{ Session::get('notify_operacion_previa') }}"></span>
                <span ng-hide="true" ng-init="mensaje_operacion_previa='{{ Session::get('mensaje_operacion_previa') }}'"></span>
                @if(Session::get('notify_operacion_previa') == 'false')
                    <span ng-hide="true" ng-init="codigo_error_operacion_previa='{{ Session::get('codigo_error_operacion_previa') }}'"></span>
                @endif
            @endif
            <div class="box-header with-border">
                <h3>Grupos de investigación</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12 col-md-6" id="col_sede">
                        <div class="form-group">
                            <label for="sede">Sede: </label>
                            <ui-select id="sede_select" ng-model="data.sede" ng-change="cambiaSede()" theme="bootstrap">
                            	<ui-select-match placeholder="Seleccione...">{$ $select.selected.nombre $}</ui-select-match>
                            	<ui-select-choices repeat="item in data.sedes | filter: $select.search">
                            		<div ng-bind-html="item.nombre | highlight: $select.search"></div>
                            	</ui-select-choices>
                            </ui-select>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <div class="text-right2">
                            <a id="btn_registrar_grupo" href="/grupos/registrar" class="btn btn-primary" ng-class="{'btn-block': establecer_btn_block}">Registrar nuevo grupo de investigación</a>
                        </div>
                    </div>
                </div>
                <br />
                <br />
                <div id="contenedor_tabla_grupos">
                    <div class="table-responsive custom-scrollbar">
                        <table datatable="ng" dt-options="dtOptions" class="table table-hover table-stripped table-bordered">
                            <thead>
                                <tr>
                                    <th>Grupo de investigación</th>
                                    <th>Clasificación del grupo</th>
                                    <th>Area Colciencias</th>
                                    <th>Gran area Colciencias</th>
                                    <th>Líneas de investigacion</th>
                                    <th>Editar</th>
                                    <th>Eliminar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!--<tr ng-show="visibilidad.mostrar_mensaje_operacion">-->
                                <!--    <td colspan="7" class="text-center">{$ data.mensaje_operacion $}</td>-->
                                <!--</tr>-->
                                <tr ng-show="visibilidad.mostrar_grupos_investigacion" ng-repeat="grupo_investigacion in data.grupos_investigacion">
                                    <!--<td colspan="5" class="text-center">Si hay grupos de inv para esta sede</td>-->
                                    <td>{$ grupo_investigacion.nombre_grupo_investigacion $}</td>
                                    <td>{$ grupo_investigacion.clasificacion_grupo_investigacion $}</td>
                                    <td>{$ grupo_investigacion.area $}</td>
                                    <td>{$ grupo_investigacion.gran_area $}</td>
                                    <td><button type="button" class="btn btn-default" ng-click="btn_ver_lineas_inv_click(grupo_investigacion)"><i class="fa fa-list-ul" aria-hidden="true"></i></button></td>
                                    <td><a href="/grupos/editar?id={$ grupo_investigacion.id $}" class="btn btn-default" ><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></td>
                                    <td><button type="button" class="btn btn-default" ng-click="btn_eliminar_grupo_inv_click(grupo_investigacion)"><i class="fa fa-times" aria-hidden="true"></i></button></td>
                                </tr>
                                <!--<tr ng-show="visibilidad.show_sin_grupos">-->
                                <!--    <td colspan="7" class="text-center">Sin grupos de investigación</td>-->
                                <!--</tr>-->
                            </tbody>
                        </table>
                    </div>
                    <div id="velo" ng-show="visibilidad.mostrar_velo">
                        <div id="texto_cargando" style="color:white;">Cargando...<i class="fa fa-circle-o-notch fa-spin fa-2x fa-fw"></i></div>
                    </div>
                </div>
                <div class="panel panel-default borde-rectangular">
                    <div class="panel-heading">
                        <p style="margin-bottom:0;">{$ data.titulo_panel_lineas_investigacion $}</p>
                    </div>
                    <div class="panel-body" id="div_lineas_inv">
                        <p ng-show="visibilidad.mostrar_cargando_lineas_inv" class="text-center">Cargando...<i class="fa fa-circle-o-notch fa-spin fa-2x fa-fw"></i></p>
                        <p ng-show="visibilidad.mostrar_mensaje_operacion_lineas_inv" class="text-center">{$ data.mensaje_operacion_lineas_inv $}</p>
                        <ui-select multiple ng-model="data.lineas_investigacion" theme="bootstrap" ng-show="visibilidad.mostrar_lineas_inv"
                        style="width: 100%;" title="Líneas de investigación" search-enabled="false">
                            <ui-select-match placeholder="Líneas de investigación" ui-lock-choice="true">{$ $item.nombre $}</ui-select-match>
                            <ui-select-choices repeat="linea_inv in data.lineas_investigacion" ui-disable-choice="true">
                                {$ linea_inv $}
                            </ui-select-choices>
                        </ui-select>
                    </div>
                </div>
            </div>
        </div>
    </section>
    

@stop <!--Stop section 'contenido'-->

@section('post_scripts')
    @if(isset($post_scripts))
        @foreach($post_scripts as $script) 
            <script type="text/javascript" src="/app/js/{{ $script }}"></script>
        @endforeach
    @endif
@stop <!--Stop section 'post_scripts'-->


@if(isset($angular_sgpi_app_extra_dependencies))
    @section('post_sgpi_app_dependencies')
        <script>
            @foreach($angular_sgpi_app_extra_dependencies as $dependencie) 
                sgpi_app.requires.push('{{ $dependencie }}');
            @endforeach
        </script>
    @stop
@endif