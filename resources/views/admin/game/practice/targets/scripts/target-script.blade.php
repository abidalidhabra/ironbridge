<script>

    /** Add Clue **/
    let clueURL = "{{ route('admin.practiceGame.clue.html') }}"
    $(document).on('click', '.add-target', function() {
        // if(validate()){
            let lastClueIndex = parseInt($('#last-token').val());
            $.get(clueURL, {index: (lastClueIndex+1)}, function(response) {
                $('.clues').append(response);
            }).always(function() {
                // add minus button with plus button
                let btnOfLastClue = $('.clues').children('.clue').last().find('.add-target');
                //$('<button type="button" class="btn btn-danger remove-target">-</button>').insertBefore(btnOfLastClue);
                $('#last-token').val(lastClueIndex+1);
            });

            let clues = $('.clues').children('.clue');
            if(clues.last().find('.remove-target').length > 0) {
                // remove the plus button if div have both plus & minus button
                clues.last().find('.add-target').remove();
            }else{
                // convert plus button to minus button
                $(this).removeClass('btn-success add-target').addClass('btn-danger remove-target').text('-');
            }

        // }
    });

    $(document).on('click', '.remove-target', function() {
        $(this).parents('.targetsbox').remove();
        let clues = $('.clues').children('.clue');

        if(clues.length > 1) {
            if (clues.last().find('.add-target').length <= 0) {
                // add plus button to last clue only if clues doen't hold plus button and clue are more than 1
                let btnOfLastClue = clues.last().find('.remove-target');
                $('<button type="button" class="btn btn-success add-target">+</button>').insertBefore(btnOfLastClue);
            }
        }else {
            if(clues.last().find('.add-target').length > 0) {
                // remove the minus button if div have both plus & minus button
                clues.last().find('.remove-target').remove();
            }else{
                // convert minus button to plus button
                let btnOfLastClue = clues.last().find('.remove-target');
                $(btnOfLastClue).removeClass('btn-danger remove-target').addClass('btn-success add-target').text('+');
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