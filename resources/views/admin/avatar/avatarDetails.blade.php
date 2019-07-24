@section('title','Ironbridge1779 | Avatar')
@extends('admin.layouts.admin-app')
@section('styles')
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/toastr.min.css') }}"> -->
@endsection
@section('content')
    <div class="right_paddingboxpart">
        <div class="datingactivity_box">
            <div class="backbtn">
                <a href="{{ route('admin.userList') }}">Back</a>
            </div>
            <h3>Avatar Details</h3>
            <div class="innerdatingactivity">
                <div class="swoped_detlisbox">
                    <div class="swoped_detlisleft">
                       <p>Name</p> 
                    </div>
                    <div class="swoped_detlisright">
                        <span>{{ $avatar->name }}</span>
                    </div>
                </div>
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
                            <div class="px20_20 colors" data-colorcode="{{ $hairsColor }}" data-status="hairs_color" data-index="{{ $key }}" id="hairs_color{{$key}}" style="background: {{ $hairsColor }}"></div>
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
                    <img class="card-img-top" src="{{ asset('admin_assets/images/FullDressup.png') }}">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-9">
                                <h5 class="card-title">${{ $widget->gold_price }}</h5>
                            </div>
                            <div class="col-md-3 text-right">
                                <a href="javascript:void(0)" class="widget_edit" id="widget_edit{{ $widget->id}}" data-gold="{{ $widget->gold_price }}" data-id="{{ $widget->id}}"><i class="fa fa-pencil iconsetaddbox"></i>
                                </a>    
                                <a href="javascript:void(0)" class="widget_save hidden" id="widget_save{{ $widget->id}}" data-id="{{ $widget->id}}"><i class="fa fa-save iconsetaddbox"></i></a> 
                            </div>
                        </div>
                        <p class="card-text">{{ $widget->id}}</p>
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
                $(this).parents('.row').find('.card-title').addClass('hidden').after('<input type="number" class="gold_price"  value="'+gold+'">');
            }) 

            /** save widget in price **/
            $(document).on('click','.widget_save',function(){
                var id = $(this).data('id');
                var gold = $(this).parents('.row').find('.gold_price').val();
                $.ajax({
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ route("admin.widgetPriceUpdate") }}',
                    data: {id : id,gold_price:gold},
                    success: function(response)
                    {
                        if (response.status == true) {
                            toastr.success(response.message);
                            $('#widget_save'+id).addClass('hidden');
                            $('#widget_edit'+id).removeClass('hidden').attr('data-gold',gold);
                            $('#widget_edit'+id).parents('.row').find('.card-title').removeClass('hidden').text('$'+gold);
                            $('#widget_edit'+id).parents('.row').find('.gold_price').remove();
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
                $('.color_code').remove();
                $(this).parents('.swoped_detlisright').append('<input type="text" data-id="'+$(this).attr('data-index')+'" data-status="'+status+'" value="'+color_code+'" class="color_code">');
            });

            $(document).on('focusout','.color_code',function(){
                var status = $(this).attr('data-status');
                var color_code = $(this).val();
                var index = $(this).attr('data-id');
                var id = '{{ $avatar->id }}';

                var valid_color_code  = /(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/i.test(color_code);
                if (valid_color_code) {
                    $.ajax({
                        type: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: '{{ route("admin.avatarColorUpdate") }}',
                        data: {id : id,status:status,color_code:color_code,index:index},
                        success: function(response)
                        {
                            if (response.status == true) {
                                toastr.success(response.message);
                                $('#'+status+index).css("background",color_code);
                                $('.color_code').remove();                            
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
        });
    </script>
@endsection