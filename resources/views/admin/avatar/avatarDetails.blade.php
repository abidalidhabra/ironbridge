@section('title','Ironbridge1779 | Avatar')
@extends('admin.layouts.admin-app')
@section('styles')
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/toastr.min.css') }}"> -->
@endsection
@section('content')
    <div class="right_paddingboxpart">
        <div class="datingactivity_box">
            <div class="backbtn">
                <a href="{{ route('admin.avatar.index') }}">Back</a>
            </div>
            <h3>Avatar Details</h3>
            <div class="innerdatingactivity">
                <!-- <div class="swoped_detlisbox">
                    <div class="swoped_detlisleft">
                       <p>Name</p> 
                    </div>
                    <div class="swoped_detlisright">
                        <span>{{ $avatar->name }}</span>
                    </div>
                </div> -->
                <div class="swoped_detlisbox">
                    <div class="swoped_detlisleft">
                       <p>Gender</p> 
                    </div>
                    <div class="swoped_detlisright">
                        <p>{{ ($avatar->gender)?$avatar->gender:'-' }}</p>
                    </div>
                </div>
                <div class="swoped_detlisbox">
                    <div class="swoped_detlisleft">
                       <p>Skin Colors</p> 
                    </div>
                    <div class="swoped_detlisright">
                        @forelse($avatar->skin_colors as $key => $skinColor)
                            <div class="px20_20 colors" data-colorcode="{{ $skinColor }}" data-status="skin_color" data-index="{{ $key }}" id="skin_color{{$key}}" style="background: {{ $skinColor }}"></div>
                        @empty
                        @endforelse
                    </div>
                </div>
                <div class="swoped_detlisbox">
                    <div class="swoped_detlisleft">
                       <p>Hairs Colors</p> 
                    </div>
                    <div class="swoped_detlisright">
                        @forelse($avatar->hairs_colors as $key => $hairsColor)
                            @if($hairsColor)
                            <div class="px20_20 colors" data-colorcode="{{ $hairsColor }}" data-status="hairs_color" data-index="{{ $key }}" id="hairs_color{{$key}}" style="background: {{ $hairsColor }}"></div>
                            @else
                            <div class="px20_20 colors text-center" data-colorcode="{{ $hairsColor }}" data-status="hairs_color" data-index="{{ $key }}" id="hairs_color{{$key}}"><i class="fa fa-plus"></i></div>
                            @endif
                        @empty
                        @endforelse
                    </div>
                </div>
                <div class="swoped_detlisbox">
                    <div class="swoped_detlisleft">
                       <p>Eyes Colors</p> 
                    </div>
                    <div class="swoped_detlisright">
                        @forelse($avatar->eyes_colors as $key => $eyesColor)
                            <div class="px20_20 colors" data-colorcode="{{ $eyesColor }}" data-status="eyes_colors" data-index="{{ $key }}" id="eyes_colors{{$key}}" style="background: {{ $eyesColor }}"></div>
                        @empty
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        <div class="avtardetailbox">
            @forelse($widgetItem as $key => $widgetlist)
                <h4>{{ $key }}</h4>
                @forelse($widgetlist as $widget)
                <div class="avtarimgtextiner">
                    <img class="card-img-top" src="{{ asset('admin_assets/widgets/'.$widget->id.'.png') }}">
                    <div class="card-body">
                        <div class="col-md-8">
                            <div class="row">
                                <h5 class="card-title">{{ $widget->gold_price }} Gold</h5>
                            </div>
                        </div>
                        <div class="col-md-4 text-right">
                            <div class="row">
                                <a href="javascript:void(0)" class="widget_edit" id="widget_edit{{ $widget->id}}" data-gold="{{ $widget->gold_price }}" data-id="{{ $widget->id}}"><i class="fa fa-pencil iconsetaddbox"></i>
                                </a>    
                                <a href="javascript:void(0)" class="widget_save hidden text-left" id="widget_save{{ $widget->id}}" data-id="{{ $widget->id}}"><i class="fa fa-save iconsetaddbox"></i></a>
                                <a href="javascript:void(0)" class="widget_close hidden text-right" id="widget_close{{ $widget->id}}"><i class="fa fa-times iconsetaddbox"></i></a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="radiobtnbox">
                                <label class="radio-inline">
                                    <input type="radio" class="widget_category" name="widget_category{{ $widget->id}}" data-id="{{ $widget->id}}" value="basic" {{ (($widget->widget_category == 'basic')?'checked':'') }}>Basic
                                </label>
                            </div>
                            <div class="radiobtnbox">
                                <label class="radio-inline">
                                    <input type="radio" class="widget_category" name="widget_category{{ $widget->id}}" data-id="{{ $widget->id}}" value="delux" {{ (($widget->widget_category == 'delux')?'checked':'') }}>Delux
                                </label>
                            </div>
                        </div>
                        <!-- <p class="card-text">{{ $widget->id}}</p> -->
                    </div>
                </div>
                @empty
                @endforelse
            @empty
            @endforelse

            
        </div>
    </div>

@endsection

@section('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            $(document).on('click','.widget_edit',function(){
                var id = $(this).data('id');
                var gold = $(this).attr('data-gold');
                $(this).addClass('hidden');
                $(this).parents('.text-right').find('.widget_save').removeClass('hidden');
                $(this).parents('.text-right').find('.widget_close').removeClass('hidden');

                $(this).parents('.card-body').find('.card-title').addClass('hidden').after('<input type="number" class="gold_price width-100"  value="'+gold+'">');
            })

            /** CLOSE BUTTON **/
            $(document).on('click','.widget_close',function(){
                $(this).addClass('hidden');
                $(this).parents('.text-right').find('.widget_save').addClass('hidden');
                $(this).parents('.text-right').find('.widget_edit').removeClass('hidden');
                $(this).parents('.card-body').find('.card-title').removeClass('hidden');
                $(this).parents('.card-body').find('.gold_price').remove();
            
            });

            /** save widget in price **/
            $(document).on('click','.widget_save',function(){
                var id = $(this).data('id');
                var gold = $(this).parents('.card-body').find('.gold_price').val();
                $.ajax({
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ route("admin.widgetPriceUpdate") }}',
                    data: {id : id,gold_price:gold},
                    beforeSend: function() {
                        $('#widget_save'+id).find('i').addClass('fa-spinner').removeClass('fa-save');
                        $('#widget_close'+id).addClass('hidden');
                    },
                    success: function(response)
                    {
                        if (response.status == true) {
                            toastr.success(response.message);
                            $('#widget_save'+id).addClass('hidden');
                            $('#widget_save'+id).find('i').removeClass('fa-spinner').addClass('fa-save');
                            $('#widget_close'+id).addClass('hidden');
                            $('#widget_edit'+id).removeClass('hidden').attr('data-gold',gold);
                            $('#widget_edit'+id).parents('.card-body').find('.card-title').removeClass('hidden').text(gold+' Gold');
                            $('#widget_edit'+id).parents('.card-body').find('.gold_price').remove();

                        } else {
                            toastr.warning(response.message);
                        }
                    }
                });
            })

            //input box open 
            $(document).on('click','.colors',function(){
                var status = $(this).attr('data-status');
                var color_code = $(this).attr('data-colorcode');
                $('.color_code , .color_code1 , .remove_color_code').remove();
                $(this).parents('.swoped_detlisright').append('<input type="text" value="'+color_code+'" class="color_code1"><a href="JavaScript:Void(0)" data-id="'+$(this).attr('data-index')+'" data-status="'+status+'" class="color_code"><i class="fa fa-save iconsetaddbox"></i></a><a href="JavaScript:Void(0)" class="remove_color_code"><i class="fa fa-times iconsetaddbox"></i></a> ');
            });

            //$(document).on('focusout','.color_code',function(){
            $(document).on('click','.color_code',function(e){
                e.preventDefault();
                var status = $(this).attr('data-status');
                var color_code = $(this).parents('.swoped_detlisright').find('.color_code1').val();
                var index = $(this).attr('data-id');
                var id = '{{ $avatar->id }}';
                var valid_color_code = "1";
                if (status != 'hairs_color') {
                    valid_color_code  = /(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/i.test(color_code);
                    console.log('success');
                }
                if (valid_color_code) {
                    $.ajax({
                        type: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: '{{ route("admin.avatarColorUpdate") }}',
                        data: {id : id,status:status,color_code:color_code,index:index},
                        beforeSend: function() {
                            $('.color_code').find('i').addClass('fa-spinner');
                            $('.remove_color_code').remove();
                        },
                        success: function(response)
                        {
                            if (response.status == true) {
                                toastr.success(response.message);
                                $('#'+status+index).removeClass('text-center').css("background",color_code).html('');
                                if (color_code == "") {
                                    $('#'+status+index).addClass('text-center').html('<i class="fa fa-plus"></i>').css("background",'');
                                }
                                $('.color_code , .color_code1, .remove_color_code').remove();                          
                            } else {
                                toastr.warning(response.message);
                            }
                        }
                    });
                } else {
                    toastr.warning('Please valid color code');
                }
            })

            // $(selector).focusout(function);
            $(document).on('click','.remove_color_code',function(){
                 $('.color_code , .color_code1 , .remove_color_code').remove();
            })

            /** widget category **/
            $(document).on('click','.widget_category',function(){
                if($(this).is(":checked")){
                    var category = $(this).val();
                }
                var id = $(this).data('id');
                $.ajax({
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ route("admin.widgetCategoryUpdate") }}',
                    data: {id : id,category:category},
                    success: function(response)
                    {
                        if (response.status == true) {
                            toastr.success(response.message);      
                        } else {
                            toastr.warning(response.message);
                        }
                    }
                });
            });
        });
    </script>
@endsection