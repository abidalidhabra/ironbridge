@section('title','Ironbridge1779 | Event Notifications')

@extends('admin.layouts.admin-app')

@section('styles')
@endsection

@section('content')
<div class="right_paddingboxpart">      
    <div class="users_datatablebox">
        <div class="row">
            <div class="col-md-6">
                <h3>Event Notifications</h3>
            </div>
        </div>
    </div>
    <br/><br/>
    @if($cities->count())
        <div class="customdatatable_box">
            <form method="POST" id="sendEventNotificationForm" enctype="multipart/form-data">
                @csrf
                
                <div class="allbasicdirmain" style="margin-top: 5px;">                
                    <div class="allbasicdirbox">                

                        <div class="row">
                            
                            <div class="form-group col-md-3">
                                <label class="control-label" data-toggle="tooltip" data-title="Where you want to broadcast the message." data-placement="right">
                                    Send To: <i class="fa fa-question-circle"></i>
                                </label>
                                <select name="target" class="form-control" id="js-target">
                                    <option value="">Select a target</option>
                                    <option value="BYCOUNTRY">Country</option>
                                    <option value="BYCITY">City</option>
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label class="control-label" data-toggle="tooltip" data-title="Select the target audience." data-placement="right">
                                    Target Audience: <i class="fa fa-question-circle"></i>
                                </label>
                                <select name="target_audience" class="form-control" id="js-target-audience">
                                    <option value="">Select a target first.</option>
                                </select>
                            </div>

                            <div class="form-group col-md-4 d-none" id="js-cities-container">
                                <label class="control-label" data-toggle="tooltip" data-title="This shows all the cities having events within a month." data-placement="right">
                                    Cities: <i class="fa fa-question-circle"></i>
                                </label>
                                <select name="cities[]" class="form-control" id="js-cities" multiple="multiple">
                                    @forelse($cities as $city)
                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                    @empty
                                        <option value="">No city are there.</option>
                                    @endforelse
                                </select>
                            </div>

                            <div class="form-group col-md-4 d-none" id="js-countries-container">
                                <label class="control-label" data-toggle="tooltip" data-title="This shows all the countries having events within a month." data-placement="right">
                                    Countries: <i class="fa fa-question-circle"></i>
                                </label>
                                <select name="countries[]" class="form-control" id="js-countries" multiple="multiple">
                                    @forelse($cities as $city)
                                        <option value="{{ $city->country->id }}">{{ $city->country->name }}</option>
                                    @empty
                                        <option value="">No country are there.</option>
                                    @endforelse
                                </select>
                            </div>

                        </div>

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="control-label" data-toggle="tooltip" data-title="Write a title of message." data-placement="right">
                                    Title: <i class="fa fa-question-circle"></i>
                                </label>
                                <input type="text" name="title" class="form-control" id="title">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="control-label" data-toggle="tooltip" data-title="Write a title of message." data-placement="right">
                                    Message: <i class="fa fa-question-circle"></i>
                                </label>
                                <textarea name="message" class="form-control" id="message" rows="3"></textarea>
                            </div>
                            <div class="form-group col-md-12">
                                <button type="submit" class="btn btn-success btnSubmit">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @else
        <div class="row">
            <div class="col-md-12 text-center">
                <h3>No upcoming events are there.</h3>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script type="text/javascript">
        $('[data-toggle="tooltip"]').tooltip(); 
        
        $(document).on('change', '#js-target', function(){
            if (this.value == 'BYCOUNTRY') {
                $('#js-countries-container').show();
                $('#js-countries').select2();
                $('#js-cities-container').hide();
                initTargetAudienceSelect('country');
            }else{
                $('.targetRaflection').html('city');
                $('#js-cities-container').show();
                $('#js-cities').select2();
                $('#js-countries-container').hide();
                initTargetAudienceSelect('city');
            }
        });

        function initTargetAudienceSelect(target) {
            let element = $('#js-target-audience');
            element.empty();
            element.append($('<option>', {
                value:  '',
                text:   `Select Audience`
            }));
            element.append($('<option>', {
                value:  'LOCALS',
                text:   `To those who are in same ${target}`
            }));
            element.append($('<option>', {
                value:  '!LOCALS',
                text:   `To those who are not in same ${target}`
            }));
        }

        $(document).on('submit', '#sendEventNotificationForm', function(e){
            e.preventDefault();
            $.ajax({
                type:'POST',
                url:'{{ route("admin.event-notifications.store") }}',
                data: $(this).serialize(),
                beforeSend:function(){},
                success:function(response) {
                    toastr.success(response.message);
                },
                complete:function(){},
                error:function(jqXHR, textStatus, errorThrown){
                    toastr.error(JSON.parse(jqXHR.responseText).message);
                }
            });
        });
</script>
@endsection