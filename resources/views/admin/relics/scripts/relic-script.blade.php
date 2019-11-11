<script>

    /** Add Clue **/
    let clueURL = "{{ route('admin.relics.clue.html') }}"
    $(document).on('click', '.add-clue', function() {
        // if(validate()){
            let lastClueIndex = parseInt($('#last-token').val());
            $.get(clueURL, {index: (lastClueIndex+1)}, function(response) {
                $('.clues').append(response);
            }).always(function() {
                // add minus button with plus button
                let btnOfLastClue = $('.clues').children('.clue').last().find('.add-clue');
                $('<button type="button" class="btn btn-danger remove-clue">-</button>').insertBefore(btnOfLastClue);
                $('#last-token').val(lastClueIndex+1);
            });

            let clues = $('.clues').children('.clue');
            if(clues.last().find('.remove-clue').length > 0) {
                // remove the plus button if div have both plus & minus button
                clues.last().find('.add-clue').remove();
            }else{
                // convert plus button to minus button
                $(this).removeClass('btn-success add-clue').addClass('btn-danger remove-clue').text('-');
            }

        // }
    });

    $(document).on('click', '.remove-clue', function() {
        $(this).parent().remove();
        let clues = $('.clues').children('.clue');

        if(clues.length > 1) {
            if (clues.last().find('.add-clue').length <= 0) {
                // add plus button to last clue only if clues doen't hold plus button and clue are more than 1
                let btnOfLastClue = clues.last().find('.remove-clue');
                $('<button type="button" class="btn btn-success add-clue">+</button>').insertAfter(btnOfLastClue);
            }
        }else {
            if(clues.last().find('.add-clue').length > 0) {
                // remove the minus button if div have both plus & minus button
                clues.last().find('.remove-clue').remove();
            }else{
                // convert minus button to plus button
                let btnOfLastClue = clues.last().find('.remove-clue');
                $(btnOfLastClue).removeClass('btn-danger remove-clue').addClass('btn-success add-clue').text('+');
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