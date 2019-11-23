<script>

    /** Add Clue **/
    let clueURL = "{{ route('admin.loots.goldHTML') }}"
    $(document).on('click', '.add-gold', function() {
        // if(validate()){
            let lastClueIndex = parseInt($('#last-token').val());
            $.get(clueURL, {index: (lastClueIndex+1)}, function(response) {
                $('.golds').append(response);
            }).always(function() {
                // add minus button with plus button
                let btnOfLastClue = $('.golds').children('.gold').last().find('.add-gold');
                $('<button type="button" class="btn btn-danger remove-gold">-</button>').insertBefore(btnOfLastClue);
                $('#last-token').val(lastClueIndex+1);
            });

            let golds = $('.golds').children('.gold');
            if(golds.last().find('.remove-gold').length > 0) {
                // remove the plus button if div have both plus & minus button
                golds.last().find('.add-gold').remove();
            }else{
                // convert plus button to minus button
                $(this).removeClass('btn-success add-gold').addClass('btn-danger remove-gold').text('-');
            }

        // }
    });

    $(document).on('click', '.remove-gold', function() {
        $(this).parent().remove();
        let golds = $('.golds').children('.gold');

        if(golds.length > 1) {
            if (golds.last().find('.add-gold').length <= 0) {
                // add plus button to last Gold only if golds doen't hold plus button and Gold are more than 1
                let btnOfLastGold = golds.last().find('.remove-gold');
                $('<button type="button" class="btn btn-success add-gold">+</button>').insertAfter(btnOfLastGold);
            }
        }else {
            if(golds.last().find('.add-gold').length > 0) {
                // remove the minus button if div have both plus & minus button
                golds.last().find('.remove-gold').remove();
            }else{
                // convert minus button to plus button
                let btnOfLastGold = golds.last().find('.remove-gold');
                $(btnOfLastGold).removeClass('btn-danger remove-gold').addClass('btn-success add-gold').text('+');
            }
        }
    });


    $(document).on('click', '.add-skeleton', function() {
        // if(validate()){
            let lastClueIndex = parseInt($('#last-token').val());
            $.get("{{ route('admin.loots.skeletonHTML') }}", {index: (lastClueIndex+1)}, function(response) {
                $('.skeleton_keys').append(response);
            }).always(function() {
                // add minus button with plus button
                let btnOfLastClue = $('.skeleton_keys').children('.skeleton_key').last().find('.add-skeleton');
                $('<button type="button" class="btn btn-danger remove-skeleton_key">-</button>').insertBefore(btnOfLastClue);
                $('#last-token').val(lastClueIndex+1);
            });

            let skeleton_keys = $('.skeleton_keys').children('.skeleton_key');
            if(skeleton_keys.last().find('.remove-skeleton_key').length > 0) {
                // remove the plus button if div have both plus & minus button
                skeleton_keys.last().find('.add-skeleton').remove();
            }else{
                // convert plus button to minus button
                $(this).removeClass('btn-success add-skeleton').addClass('btn-danger remove-skeleton_key').text('-');
            }

        // }
    });

    $(document).on('click', '.remove-skeleton_key', function() {
        $(this).parent().remove();
        let skeleton_keys = $('.skeleton_keys').children('.skeleton_key');

        if(skeleton_keys.length > 1) {
            if (skeleton_keys.last().find('.add-skeleton').length <= 0) {
                // add plus button to last skeleton_key only if skeleton_keys doen't hold plus button and skeleton_key are more than 1
                let btnOfLastSkeleton_key = skeleton_keys.last().find('.remove-skeleton_key');
                $('<button type="button" class="btn btn-success add-skeleton">+</button>').insertAfter(btnOfLastSkeleton_key);
            }
        }else {
            if(skeleton_keys.last().find('.add-skeleton').length > 0) {
                // remove the minus button if div have both plus & minus button
                skeleton_keys.last().find('.remove-skeleton_key').remove();
            }else{
                // convert minus button to plus button
                let btnOfLastSkeleton_key = skeleton_keys.last().find('.remove-skeleton_key');
                $(btnOfLastSkeleton_key).removeClass('btn-danger remove-skeleton_key').addClass('btn-success add-skeleton').text('+');
            }
        }
    });


    $(document).on('click', '.add-skeleton_gold', function() {
        // if(validate()){
            let lastClueIndex = parseInt($('#last-token').val());
            $.get("{{ route('admin.loots.skeletonGoldHTML') }}", {index: (lastClueIndex+1)}, function(response) {
                $('.skeleton_golds').append(response);
            }).always(function() {
                // add minus button with plus button
                let btnOfLastClue = $('.skeleton_golds').children('.skeleton_gold').last().find('.add-skeleton_gold');
                $('<button type="button" class="btn btn-danger remove-skeleton_gold">-</button>').insertBefore(btnOfLastClue);
                $('#last-token').val(lastClueIndex+1);
            });

            let skeleton_golds = $('.skeleton_golds').children('.skeleton_gold');
            if(skeleton_golds.last().find('.remove-skeleton_gold').length > 0) {
                // remove the plus button if div have both plus & minus button
                skeleton_golds.last().find('.add-skeleton_gold').remove();
            }else{
                // convert plus button to minus button
                $(this).removeClass('btn-success add-skeleton_gold').addClass('btn-danger remove-skeleton_gold').text('-');
            }

        // }
    });

    $(document).on('click', '.remove-skeleton_gold', function() {
        $(this).parent().remove();
        let skeleton_golds = $('.skeleton_golds').children('.skeleton_gold');

        if(skeleton_golds.length > 1) {
            if (skeleton_golds.last().find('.add-skeleton_gold').length <= 0) {
                // add plus button to last skeleton_gold only if skeleton_golds doen't hold plus button and skeleton_gold are more than 1
                let btnOfLastSkeleton_gold = skeleton_golds.last().find('.remove-skeleton_gold');
                $('<button type="button" class="btn btn-success add-skeleton_gold">+</button>').insertAfter(btnOfLastSkeleton_gold);
            }
        }else {
            if(skeleton_golds.last().find('.add-skeleton_gold').length > 0) {
                // remove the minus button if div have both plus & minus button
                skeleton_golds.last().find('.remove-skeleton_gold').remove();
            }else{
                // convert minus button to plus button
                let btnOfLastSkeleton_gold = skeleton_golds.last().find('.remove-skeleton_gold');
                $(btnOfLastSkeleton_gold).removeClass('btn-danger remove-skeleton_gold').addClass('btn-success add-skeleton_gold').text('+');
            }
        }
    });
    

    $(document).on('click', '.add-avatar', function() {
        // if(validate()){
            let lastClueIndex = parseInt($('#last-token').val());
            $.get("{{ route('admin.loots.avatarHTML') }}", {index: (lastClueIndex+1)}, function(response) {
                $('.avatars').append(response);
            }).always(function() {
                // add minus button with plus button
                let btnOfLastClue = $('.avatars').children('.avatar').last().find('.add-avatar');
                $('<button type="button" class="btn btn-danger remove-avatar">-</button>').insertBefore(btnOfLastClue);
                $('#last-token').val(lastClueIndex+1);
            });

            let avatars = $('.avatars').children('.avatar');
            if(avatars.last().find('.remove-avatar').length > 0) {
                // remove the plus button if div have both plus & minus button
                avatars.last().find('.add-avatar').remove();
            }else{
                // convert plus button to minus button
                $(this).removeClass('btn-success add-avatar').addClass('btn-danger remove-avatar').text('-');
            }

        // }
    });

    $(document).on('click', '.remove-avatar', function() {
        $(this).parent().remove();
        let avatars = $('.avatars').children('.avatar');

        if(avatars.length > 1) {
            if (avatars.last().find('.add-avatar').length <= 0) {
                // add plus button to last avatar only if avatars doen't hold plus button and avatar are more than 1
                let btnOfLastAvatar = avatars.last().find('.remove-avatar');
                $('<button type="button" class="btn btn-success add-avatar">+</button>').insertAfter(btnOfLastAvatar);
            }
        }else {
            if(avatars.last().find('.add-avatar').length > 0) {
                // remove the minus button if div have both plus & minus button
                avatars.last().find('.remove-avatar').remove();
            }else{
                // convert minus button to plus button
                let btnOfLastAvatar = avatars.last().find('.remove-avatar');
                $(btnOfLastAvatar).removeClass('btn-danger remove-avatar').addClass('btn-success add-avatar').text('+');
            }
        }
    });

    $(document).on('click', '.add-widget', async function() {
        // if(validate()){
            // let lastClueIndex = parseInt($('#last-token').val());
            let lastClueIndex = $(this).parents('.avatar').attr('index');
            let currentIndex = $(this).parents('.widgets').find('.widget').length;
            let clickedElement = $(this);
            await $.get("{{ route('admin.loots.widgetHTML') }}", {parent_index: (lastClueIndex),current_index:currentIndex}, function(response) {
                $(clickedElement).parents('.widgets').append(response);
            }).always(function() {
                // add minus button with plus button
                let btnOfLastClue = $(clickedElement).parents('.widgets').children('.widget').last().find('.add-widget');
                $('<button type="button" class="btn btn-danger remove-widget">Remove widget</button>').insertBefore(btnOfLastClue);
                // $('#last-token').val(lastClueIndex+1);
            });

            // let widgets = $(this).parents('.widgets').children('.widget');
            let widget = $(this).parents('.widget');
            if(widget.find('.remove-widget').length > 0) {
                // remove the plus button if div have both plus & minus button
                widget.find('.add-widget').remove();
            }else{
                // convert plus button to minus button
                $(this).removeClass('btn-success add-widget').addClass('btn-danger remove-widget').text('Remove widget');
            }

        // }
    });

    $(document).on('click', '.remove-widget', function() {
        let contextAvatarIndex = $(this).parents('.avatar').attr('index');
        $(this).parents('.widget').remove();
        let avatarElement = $('.avatar[index="'+contextAvatarIndex+'"]');
        let widgets = $(avatarElement).children('.widgets').children('.widget');
        if($(widgets).length > 1) {
            if ($(widgets).last().find('.add-widget').length <= 0) {
                // add plus button to last widget only if widgets doen't hold plus button and widget are more than 1
                let btnOfLastWidget = widgets.last().find('.remove-widget');
                $('<button type="button" class="btn btn-success add-widget">Add Widget</button>').insertAfter(btnOfLastWidget);
            }
        }else {
            if($(widgets).last().find('.add-widget').length > 0) {
                // remove the minus button if div have both plus & minus button
                $(widgets).last().find('.remove-widget').remove();
            }else{
                // convert minus button to plus button
                let btnOfLastWidget = $(widgets).last().find('.remove-widget');
                $(btnOfLastWidget).removeClass('btn-danger remove-widget').addClass('btn-success add-widget').text('Add widget');
            }
        }
    });




    let fails;

    function hasAttr(element, attribute) {
        var attr = $(element).attr(attribute);
        if (typeof attr !== typeof undefined && attr !== false) {
            return {status: true, value: attr};
        }else{
            return {status: false, value: attr};
        }
    }

    function focusTheError(element, message) {
        $(element).focus();
        $(element).parent().removeClass('has-success').addClass('has-error');
        fails++;
        toastr.error(message);
        return true;
    }

    function validateFromAttribute(element) {
        let value = $(element).val();
        let minimumRule = hasAttr(element, 'minlength');
        let requiredRule = hasAttr(element, 'required');

        if(requiredRule.status && !value) {
            focusTheError(element, $(element).attr('alias-name') + " is required field.");
            return false;
        }

        if(minimumRule.status && parseInt(minimumRule.value) > parseInt(value.length)) {
            focusTheError(element, $(element).attr('alias-name') + " is must be atleast 5 in length.");
            return false;
        } 

        if(fails == 0) {
            return true;
        }
    }

    function validate() {
        fails = 0;
        $("#addRelicForm :input").each(function() {
            if($(this).attr('type') !="hidden" && $(this).attr('type') !="button" && $(this).attr('type') !="submit"){
                if(!validateFromAttribute($(this))){
                    return false;
                }else{
                    $(this).parent().removeClass('has-error').addClass('has-success');
                }
            }
        });
        if (fails > 0) {
            return false
        }else {
            return true
        }
    }
</script>