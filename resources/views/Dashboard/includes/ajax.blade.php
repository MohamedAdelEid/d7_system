<script>
    $(document).on("click",".fire-popup", function(){	
        var url = $(this).attr('data-url');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });
        $.ajax({
            async: false,
            method: "get",
            url: url,
            data: {
                // product_id: product_id,
            },
        success: function (data) {
            console.log(data);
            $('.my-popup .modal-title').html(data.title);
            $('.my-popup .modal-body').html(data.body);
        },
        error: function (data) {
            alert('false');
        }
        });
    })

    $(document).on("click",".delete-popup", function(){	
        var delete_url = $(this).attr('data-url');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });
        $.ajax({
            async: false,
            method: "get",
            url: "{{route('DeletePopup')}}",
            data: {
                delete_url: delete_url,
            },
        success: function (data) {
            $('.my-popup .modal-title').html(data.title);
            $('.my-popup .modal-body').html(data.body);
        },
        error: function (data) {
            alert('false');
        }
        });
    })

    $(document).on("change",".mainCategoryIdAjax", function(){	
        var category_id = $(".mainCategoryIdAjax").val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });

        $.ajax({
            async: false,
            method: "get",
            url: "{{route('dashboard.categories.subCategoriesAjax')}}",
            data: {
                category_id: category_id,
        },
        
        success: function (data) {
            $(".mainCategoryIdDev").html(data);
        },
        error: function (data) {
            alert('false');
        }
        });
    })

    $("#AddAsSubUnitAjax").change(function() {
        if(this.checked){
            $(".addAsSubUnitDev").show();
        } else {
            $(".addAsSubUnitDev").hide();
        }
    });
    
    $(document).on("change",".mainUnitIdAjax", function(){	
        var unit_id = $(".mainUnitIdAjax").val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });

        $.ajax({
            async: false,
            method: "get",
            url: "{{route('dashboard.units.subUnitsAjax')}}",
            data: {
                unit_id: unit_id,
        },
        
        success: function (data) {
            $(".mainUnitIdDev").html(data);
        },
        error: function (data) {
            alert('false');
        }
        });
    })
</script>