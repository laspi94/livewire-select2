<script type="text/javascript">
    window.LivewireSelect2Config = @json(config('livewire-select2.error_template'));

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function(jqXHR, settings) {
            settings.data = $.extend(settings.data || {}, {
                _token: "{{ csrf_token() }}"
            });
        }
    });

    function createErrorElement(errorMessage) {
        let template = window.LivewireSelect2Config?.error_template ||
            '<span class="invalid-feedback" role="alert"><strong>:message</strong></span>';

        let errorHtml = template.replace(':message', errorMessage);

        return $(errorHtml);
    }

    $(document).ready(function() {
        window.addEventListener('loadSelect2', (event) => {
            Object.entries(event.detail[0].data).map(([key, input], i) => {
                const element = $(`[name="${key}"]`);
                loadSelect2Field(element, input.route, false, input.placeholder, input.defaultValue, input.queryParams, input.showIdOption, input.paginate);
            });
        });

        window.addEventListener("validateSelect2Field", (event) => {
            handleValidationErrorSelect2(
                event.detail[0].field,
                event.detail[0].message
            );
        });

        function handleValidationErrorSelect2(fieldName, errorMessage) {
            const field = $('[name="' + fieldName + '"]');

            if (field.length === 0) return;

            const select2Container = field.next(".select2-container");

            const feedbackElement = select2Container.next(".invalid-feedback");

            if (errorMessage) {
                if (feedbackElement.length === 0) {

                    select2Container.after(createErrorElement(errorMessage));

                    field.addClass("is-invalid");
                }
            } else {
                if (feedbackElement.length > 0) {
                    feedbackElement.remove();
                }

                field.removeClass("is-invalid");
            }
        }

        window.addEventListener("resetValidateSelect2Field", (event) => {
            handleValidationResetSelect2(event.detail[0].field);
        });

        function handleValidationResetSelect2(fieldName) {
            const field = $('[name="' + fieldName + '"]');

            if (field.length === 0) return;

            const select2Container = field.next(".select2-container");

            const feedbackElement = select2Container.next(".invalid-feedback");

            if (feedbackElement.length > 0) {
                feedbackElement.remove();
            }

            field.removeClass("is-invalid");
        }

        $(".select2").on("change", function() {
            const fieldName = $(this).attr("name");
            const fieldValue = $(this).val();

            Livewire.dispatch("select2Updated", {
                field: fieldName,
                value: fieldValue,
            });
        });

        $("select.select2:not(.normal)").each(function() {
            $(this).select2({
                theme: "bootstrap-5",
                dropdownParent: $(this).parent().parent(),
            });
        });
    });

    async function loadSelect2Field(
        element,
        route,
        nullable = true,
        placeholder = "Seleccione una opción",
        initialValue = null,
        queryParams = null,
        showIdOption = true,
        paginate = 20
    ) {
        if (!initialValue) {
            initialValue = {
                id: '-1',
                text: placeholder
            };
        }

        element.select2({
            theme: '{{ config('livewire-select2.theme') }}',
            placeholder: placeholder,
            allowClear: nullable,
            dropdownParent: element.parent().parent(),
            ajax: {
                url: route,
                dataType: 'json',
                delay: 300,
                data: function(params) {
                    return {
                        queryParams: queryParams,
                        paginate: paginate,
                        initialValue: initialValue.id,
                        q: params.term ?? '',
                        page: params.page || 1
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;

                    let results = data.results.map(item => ({
                        id: item.id,
                        text: showIdOption ? `${item.id} - ${item.text}` : `${item.text}`
                    }));

                    if (params.page === 1 && !nullable) {
                        results.unshift({
                            id: initialValue.id,
                            text: initialValue.text
                        });
                    }

                    return {
                        results: results,
                        pagination: {
                            more: data.pagination.more
                        }
                    };
                },
                cache: true
            }
        });

        if (initialValue) {
            const {
                id,
                text
            } = initialValue;

            const newText = showIdOption && id != '-1' ? `${id} - ${text}` : `${text}`;

            element.append(
                $("<option>", {
                    value: id,
                    text: newText,
                    selected: true,
                })
            );

            element.trigger("change");
        }
    }
</script>
