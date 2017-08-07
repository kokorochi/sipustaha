/**
 * Created by Surya on 29/05/2017.
 */
$(document).ready(function () {
    var getUrl = window.location,
        baseUrl = getUrl.protocol + "//" + getUrl.host + "/";

    if ($("#pustaha-list").length) {
        var pustahaDatatable = $("#pustaha-list").dataTable({
            autoWidth: false,
            responsive: true,
            ajax: baseUrl + 'pustahas/ajax',
            columnDefs: [
                {
                    orderable: false,
                    defaultContent: '<a data-toggle="tooltip" data-placement="top" title="Lihat Pertanyaan"><button class="btn btn-primary btn-sm rounded display"><i class="fa fa-eye" style="color:white;"></i></button></a>' +
                    '<a data-toggle="tooltip" data-placement="top" data-original-title="Delete"><button class="btn btn-danger btn-sm rounded delete" data-toggle="modal" data-target="#delete"><i class="fa fa-times"></i></button></a>',
                    targets: 7
                },
                {
                    className: "dt-center",
                    targets: [1, 4, 5, 6, 7]
                },
                {
                    width: "5%",
                    targets: 1,
                },
                {
                    visible: false,
                    targets: 0,
                }
            ],
        });

        $(document).on("click", "#pustaha-list a button.delete", function (e) {
            e.preventDefault();
            var dt_row = $(this).closest("li").data("dt-row");
            
            if (dt_row >= 0) {
                var position = dt_row;
            } else {
                var target_row = $(this).closest("tr").get(0);
                var position = pustahaDatatable.fnGetPosition(target_row);
            }
            var id = pustahaDatatable.fnGetData(position)[0];

            $("#delete form").attr("action", baseUrl + "pustahas/delete?id=" + id);

            // window.open(baseUrl + "partners/delete?id=" + partner_id);
        });

        $(document).on("click", "#pustaha-list a button.display", function (e) {
            e.preventDefault();
            var dt_row = $(this).closest("li").data("dt-row");

            if (dt_row >= 0) {
                var position = dt_row;
            } else {
                var target_row = $(this).closest("tr").get(0);
                var position = pustahaDatatable.fnGetPosition(target_row);
            }
            var id = pustahaDatatable.fnGetData(position)[0];

            window.open(baseUrl + "pustahas/display?id=" + id, "_self");
        });
    }

    if ($("#approval-pustaha-list").length) {
        var auths = $('#auths').val();
        var apprv = "";

        var wr3 = '<a class="btn btn-theme btn-sm rounded appwr3" data-toggle="tooltip" data-placement="top" title="Approval WR3">Approval</a>';
        var lp = '<a class="btn btn-danger btn-sm rounded applp" data-toggle="tooltip" data-placement="top" title="Approval LP">Approval</a>';
        if(auths=='SU'){
            var apprv = wr3 + lp;
        }else if(auths=='OWR3'){
            var apprv = wr3;
        }else if(auths=='OPEL'){
            var apprv = lp;
        }
        var pustahaDatatable = $("#approval-pustaha-list").dataTable({
            autoWidth: false,
            responsive: true,
            ajax: baseUrl + 'approvals/ajax',
            columnDefs: [
                {
                    orderable: false,
                    defaultContent: apprv,
                    targets: 8
                },
                {
                    className: "dt-center",
                    targets: [1, 4, 5, 6, 7, 8]
                },
                {
                    width: "5%",
                    targets: 1,
                },
                {
                    visible: false,
                    targets: 0,
                }
            ],
        });

        $(document).on("click", "#approval-pustaha-list a.appwr3", function (e) {
            e.preventDefault();
            var dt_row = $(this).closest("li").data("dt-row");

            if (dt_row >= 0) {
                var position = dt_row;
            } else {
                var target_row = $(this).closest("tr").get(0);
                var position = pustahaDatatable.fnGetPosition(target_row);
            }
            var id = pustahaDatatable.fnGetData(position)[0];

            window.open(baseUrl + "approvals/detail?id=" + id + "&type=wr3", "_self");
        });

        $(document).on("click", "#approval-pustaha-list a.applp", function (e) {
            e.preventDefault();
            var dt_row = $(this).closest("li").data("dt-row");

            if (dt_row >= 0) {
                var position = dt_row;
            } else {
                var target_row = $(this).closest("tr").get(0);
                var position = pustahaDatatable.fnGetPosition(target_row);
            }
            var id = pustahaDatatable.fnGetData(position)[0];

            window.open(baseUrl + "approvals/detail?id=" + id + "&type=lp", "_self");
        });
    }

    if ($("#user-list").length) {
        var userDatatable = $("#user-list").dataTable({
            autoWidth: false,
            responsive: true,
            ajax: baseUrl + 'users/ajax',
            columnDefs: [
                {
                    orderable: false,
                    defaultContent: '<a class="btn btn-theme btn-sm rounded edit" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-pencil" style="color:white;"></i></a>' +
                    '<a data-toggle="tooltip" data-placement="top" data-original-title="Delete"><button class="btn btn-danger btn-sm rounded delete" data-toggle="modal" data-target="#delete"><i class="fa fa-times"></i></button></a>',
                    targets: 4
                },
                {
                    className: "dt-center",
                    targets: [0, 3, 4]
                },
                {
                    width: "5%",
                    targets: 0,
                },
            ],
        });

        $(document).on("click", "#user-list a button.delete", function (e) {
            e.preventDefault();
            var dt_row = $(this).closest("li").data("dt-row");

            if (dt_row >= 0) {
                var position = dt_row;
            } else {
                var target_row = $(this).closest("tr").get(0);
                var position = userDatatable.fnGetPosition(target_row);
            }
            var username = userDatatable.fnGetData(position)[1];

            $("#delete form").attr("action", baseUrl + "users/delete?username=" + username);
        });

        $(document).on("click", "#user-list a.edit", function (e) {
            e.preventDefault();
            var dt_row = $(this).closest("li").data("dt-row");

            if (dt_row >= 0) {
                var position = dt_row;
            } else {
                var target_row = $(this).closest("tr").get(0);
                var position = userDatatable.fnGetPosition(target_row);
            }
            var username = userDatatable.fnGetData(position)[1];

            window.open(baseUrl + "users/edit?id=" + username, "_self");
        });
    }

    toggleCoopDetail(false, false, false, false, false);

    if ($("select[name=pustaha_type]").val() == "BUKU") {
        toggleCoopDetail(true, false, false, false, false);
    }
    else if ($("select[name=pustaha_type]").val() == "JURNAL-N" || $("select[name=pustaha_type]").val() == "JURNAL-I") {
        toggleCoopDetail(false, true, false, false, false);
    }
    else if ($("select[name=pustaha_type]").val() == "PROSIDING") {
        toggleCoopDetail(false, false, true, false, false);
    }
    else if ($("select[name=pustaha_type]").val() == "HKI") {
        toggleCoopDetail(false, false, false, true, false);
    }
    else if ($("select[name=pustaha_type]").val() == "PATEN") {
        toggleCoopDetail(false, false, false, false, true);
    }

    if ($("#fileinput-upload").length) {
        var id = $("input[name=id]").attr("value");
        if (id > 0) {
            var initialPreview = "<a href='" + baseUrl + "pustahas/download-document?id=" + id + "' class='file-preview-other'>unduh</a>";
            $("#fileinput-mou-doc").fileinput({
                "showCaption": true,
                "showRemove": false,
                "showUpload": true,
                "initialPreview": [
                    initialPreview
                ],
                "browseLabel": "Pilih File...",
                "language": "en"
            });
        }
    }

    var button = '<button class="btn btn-theme rounded" type="submit" id="submit">Submit</button>'
        + '  <button class="btn btn-danger rounded" type="reset">Reset</button>';
    $('#submit').html(button);

    $("select[name=pustaha_type]").change(function () {
        if ($('select[name=pustaha_type]').val() == 'BUKU') {
            toggleCoopDetail(true, false, false, false, false);
        } else if ($('select[name=pustaha_type]').val() == 'JURNAL-N' || $('select[name=pustaha_type]').val() == 'JURNAL-I') {
            toggleCoopDetail(false, true, false, false, false);
        } else if ($('select[name=pustaha_type]').val() == 'PROSIDING') {
            toggleCoopDetail(false, false, true, false, false);
        } else if ($('select[name=pustaha_type]').val() == 'HKI') {
            toggleCoopDetail(false, false, false, true, false);
        } else if ($('select[name=pustaha_type]').val() == 'PATEN') {
            toggleCoopDetail(false, false, false, false, true);
        }
    });

    function toggleCoopDetail(type1, type2, type3, type4, type5) {
        if (type1) {
            $('#book-container').fadeIn('slow').find('input.enable, textarea, select').attr('disabled', false);
            $('#book-container').find('input[name=author]').attr('disabled', true);
        } else {
            $('#book-container').hide().find('input.enable, textarea, select').attr('disabled', true);
        }
        if (type2) {
            $('#journal-container').fadeIn('slow').find('input.enable, textarea, select').attr('disabled', false);
            $('#journal-container').find('input[name=author]').attr('disabled', true);
        } else {
            $('#journal-container').hide().find('input.enable, textarea, select').attr('disabled', true);
        }
        if (type3) {
            $('#proceeding-container').fadeIn('slow').find('input.enable, textarea, select').attr('disabled', false);
            $('#proceeding-container').find('input[name=author]').attr('disabled', true);
        } else {
            $('#proceeding-container').hide().find('input.enable, textarea, select').attr('disabled', true);
        }
        if (type4) {
            $('#hki-container').fadeIn('slow').find('input.enable, textarea, select').attr('disabled', false);
            $('#hki-container').find('input[name=author]').attr('disabled', true);
        } else {
            $('#hki-container').hide().find('input.enable, textarea, select').attr('disabled', true);
        }
        if (type5) {
            $('#patent-container').fadeIn('slow').find('input.enable, textarea, select').attr('disabled', false);
            $('#patent-container').find('input[name=author]').attr('disabled', true);
        } else {
            $('#patent-container').hide().find('input.enable, textarea, select').attr('disabled', true);
        }
    }

    if ($("input[name=upd_mode]").val() == 'display') {
        $("#pustaha-container input:visible, #pustaha-container select:visible, #pustaha-container textarea:visible").attr("disabled", true);
    }

    if ($(".select2").length) {
        $(".select2").select2();
    }

    $("#data").DataTable();
    $('#back-top').on('click', function (e) {
        e.preventDefault();
        $('html,body').animate({
            scrollTop: 0
        }, 700);
    });

    var i = 0;
    while (i < 5) {
        var element = $("#datepicker");
        if (i > 0) {
            element = $("#datepicker" + i);
        }
        if (element.length) {
            element.datepicker({
                changeMonth: true,
                changeYear: true
            });
        }
        i++;
    }

    if ($("#MoA select[name=cooperation_id]").val() > 0) {
        var id = $("#MoA select[name=cooperation_id]").val();
        if (id > 0) {
            getCoopDetail(id)
        }
    }

    $("#MoA select[name=cooperation_id]").change(function () {
        var id = $("#MoA select[name=cooperation_id]").val();
        if (id > 0) {
            getCoopDetail(id)
        }
    });

    function getCoopDetail(id) {
        $.ajax({
            url: baseUrl + 'cooperations/ajax/cooperation-detail',
            dataType: "json",
            data: {
                id: id
            },
            success: function (data) {
                $("input[name=mou_detail_partner_id]").val(data['partner_name']);
                $("input[name=mou_detail_area_of_coop]").val(data['area_of_coop']);
                $("input[name=mou_detail_sign_date]").val(data['sign_date']);
                $("input[name=mou_detail_end_date]").val(data['end_date']);
                $("input[name=mou_detail_usu_doc_no]").val(data['usu_doc_no']);
                $("input[name=mou_detail_partner_doc_no]").val(data['partner_doc_no']);
            }
        });
    }

    if ($("#tambah_kerma").length) {
        $("#tambah_kerma").validate({
            rules: {
                "item_name[]": {
                    required: true
                },
                "item_quantity[]": {
                    required: true
                },
                "item_uom[]": {
                    required: true
                },
                "item_total_amount[]": {
                    required: true
                },
                "item_annotation[]": {
                    required: true
                },
            },
            highlight: function (element) {
                $(element).parents('.form-group').addClass('has-error has-feedback');
            },
            unhighlight: function (element) {
                $(element).parents('.form-group').removeClass('has-error');
            },
            submitHandler: function (form) {
                form.submit();
            }
        });
    }

    var autocomp_opt = {
        source: function (request, response) {
            $.ajax({
                url: baseUrl + '/users/ajax/search',
                dataType: "json",
                data: {
                    query: request.term,
                    limit: 10
                },
                success: function (data) {
                    var transformed = $.map(data, function (el) {
                        return {
                            label: el.label,
                            id: el.username,
                            full_name: el.full_name
                        };
                    });
                    response(transformed);
                }
            });
        },
        select: function (event, ui) {
            $("input[name=full_name]").val(ui.item.full_name);
            $("input[name=username]").val(ui.item.id);
            $(this).parents("tr").find("input[name^=item_username]").val(ui.item.id);
            $('.search-employee').trigger('change');
        }
    };

    var autocomp_res = {
        source: function (request, response) {
            $.ajax({
                url: baseUrl + 'pustahas/search-research',
                dataType: "json",
                data: {
                    query: request.term
                },
                success: function (data) {
                    var transformed = $.map(data, function (el) {

                        return {
                            label: el.label,
                            id: el.research_id
                        };
                    });
                    response(transformed);
                }
            });
        },
        select: function (event, ui) {
            console.log(ui);
            $(this).parents("div").find("input[name=research_id]").val(ui.item.id);
            $('.search-research').trigger('change');
        }
    };

    if ($(".search-employee").length) {
        $(".search-employee:enabled").autocomplete(autocomp_opt);
    }

    if ($(".search-research").length) {
        $(".search-research:enabled").autocomplete(autocomp_res);
    }

    $('.table-add').click(function (e) {
        e.preventDefault();
        if($("#item-table").length){
            var v_table = $(this).parents(".detail-container").find(".item-table");
        }else if($("#user-auth-table").length){
            var v_table = $("#user-auth-table");
        }
        var $clone = v_table.find('tr.hide').clone(true).removeClass('hide table-line');
        if($("#item-table").length){
            $clone.find("input[name^=item_external]").attr("disabled", false);
            $clone.find("input[name^=item_username]").attr("disabled", false);
            $clone.find("input[name^=item_name]").attr("disabled", true);
            $clone.find("input[name^=item_affiliation]").attr("disabled", true);
        }else if($("#user-auth-table").length){
            $clone.find("select").attr("disabled", false);
            $clone.find("select").addClass("select2");
        }
        v_table.find('table').append($clone);
        if($("#item-table").length){
            $(".search-employee:enabled").autocomplete(autocomp_opt);
        } else if ($("#user-auth-table").length) {
            $(".select2").select2();
        }
    });

    $('.table-remove').click(function (e) {
        e.preventDefault();
        $(this).parents('tr').detach();
    });

    $(document).on("change", "input[name^=item_external]", function () {
        if ($(this).is(":checked")) {
            $(this).parents("tr").find("input[name^=item_username_display]").attr("disabled", true);
            $(this).parents("tr").find("input[name^=item_name]").attr("disabled", false);
            $(this).parents("tr").find("input[name^=item_affiliation]").attr("disabled", false);
        } else {
            $(this).parents("tr").find("input[name^=item_username_display]").attr("disabled", false);
            $(this).parents("tr").find("input[name^=item_name]").attr("disabled", true);
            $(this).parents("tr").find("input[name^=item_affiliation]").attr("disabled", true);
        }
        $(this).parents("tr").find("input").val("");
    });

    $(document).on("change", "#moa-table input[name^=item_total_amount]", function () {
        sumTotalAmount();
    })

    function sumTotalAmount() {
        var contractElement = $("input[name=contract_amount]");
        var sum = 0;
        $("input[name^=item_total_amount]:visible").each(function () {
            var value = $(this).val();
            if (value != "") {
                value = value.replace(/\,/g, "");
                sum += parseInt(value);
            }
        });
        contractElement.val(sum)
    }

    $("#MoA select[name=unit]").change(function () {
        if ($(this).val() != null) {
            $.ajax({
                url: baseUrl + 'cooperations/ajax/get-study-program',
                data: {
                    faculty: $(this).val()
                },
                dataType: "json",
                success: function (data) {
                    var subUnitElement = $("#MoA select[name=sub_unit]");
                    subUnitElement.find("option").remove();
                    subUnitElement.append("<option value='' disabled selected>Pilih Sub Unit</option>")
                    subUnitElement.select2('data', null);
                    subUnitElement.select2({placeholder: "-- Pilih Sub Unit --"});
                    $.each(data, function (k, v) {
                        subUnitElement.append("<option value='" + v["name"] + "'>" + v["name"] + "</option>")
                    });
                    subUnitElement.trigger("chosen: updated");
                }
            });
        }
    });

    if ($(".date-picker").length) {
        $(".date-picker").datepicker({
            format: 'dd-mm-yyyy'
        });
    }

    $("#pustaha-submit").click(function () {
        if ($("select[name=pustaha_type]").val() == "BUKU") {
            $('#proceeding-container').hide().find('input, textarea, select').attr('disabled', true);
            $('#journal-container').hide().find('input, textarea, select').attr('disabled', true);
            $('#hki-container').hide().find('input, textarea, select').attr('disabled', true);
            $('#patent-container').hide().find('input, textarea, select').attr('disabled', true);
        }
        else if ($("select[name=pustaha_type]").val() == "JURNAL-N" || $("select[name=pustaha_type]").val() == "JURNAL-I") {
            $('#book-container').hide().find('input, textarea, select').attr('disabled', true);
            $('#proceeding-container').hide().find('input, textarea, select').attr('disabled', true);
            $('#hki-container').hide().find('input, textarea, select').attr('disabled', true);
            $('#patent-container').hide().find('input, textarea, select').attr('disabled', true);
        }
        else if ($("select[name=pustaha_type]").val() == "PROSIDING") {
            $('#book-container').hide().find('input, textarea, select').attr('disabled', true);
            $('#journal-container').hide().find('input, textarea, select').attr('disabled', true);
            $('#hki-container').hide().find('input, textarea, select').attr('disabled', true);
            $('#patent-container').hide().find('input, textarea, select').attr('disabled', true);
        }
        else if ($("select[name=pustaha_type]").val() == "HKI") {
            $('#book-container').hide().find('input, textarea, select').attr('disabled', true);
            $('#journal-container').hide().find('input, textarea, select').attr('disabled', true);
            $('#proceeding-container').hide().find('input, textarea, select').attr('disabled', true);
            $('#patent-container').hide().find('input, textarea, select').attr('disabled', true);
        }
        else if ($("select[name=pustaha_type]").val() == "PATEN") {
            $('#book-container').hide().find('input, textarea, select').attr('disabled', true);
            $('#journal-container').hide().find('input, textarea, select').attr('disabled', true);
            $('#hki-container').hide().find('input, textarea, select').attr('disabled', true);
            $('#proceeding-container').hide().find('input, textarea, select').attr('disabled', true);
        }
        if ($(".item-table:visible").length) {
            $.each($("input[name^=item_]:visible:disabled"), function(index, value){
                $(value).attr('disabled', false);
            });
        }
    });
});