jQuery(document).ready(($) => {

    let answerable = $('#department-answerable');

    answerable.select2({
        ajax: {
            url: TKT_DATA.ajax_url,
            dataType: 'json',
            delay: 250,
            type: 'post',
            timeout: 20000,
            data: function (params) {
                return {
                    term: params.term,
                    action: 'tkt_search_users'
                }
            },
            processResults: (data) => {
                let items = [];
                if (data) {
                    $.each(data, (i, user) => {
                        items.push({id: user[0], text: user[1]});
                    })
                }

                return {
                    results: items
                }
            },
            cache: true
        }
    })

    let creatorID = $('#tkt-creator-id');

    creatorID.select2({
        ajax: {
            url: TKT_DATA.ajax_url,
            dataType: 'json',
            delay: 250,
            type: 'post',
            timeout: 20000,
            data: function (params) {
                return {
                    term: params.term,
                    action: 'tkt_search_users'
                }
            },
            processResults: (data) => {
                let items = [];
                if (data) {
                    $.each(data, (i, user) => {
                        items.push({id: user[0], text: user[1]});
                    })
                }

                return {
                    results: items
                }
            },
            cache: true
        }
    })

    let userID = $('#tkt-user-id');
    userID.select2({
        ajax: {
            url: TKT_DATA.ajax_url,
            dataType: 'json',
            delay: 250,
            type: 'post',
            timeout: 20000,
            data: function (params) {
                return {
                    term: params.term,
                    action: 'tkt_search_users'
                }
            },
            processResults: (data) => {
                let items = [];
                if (data) {
                    $.each(data, (i, user) => {
                        items.push({id: user[0], text: user[1]});
                    })
                }

                return {
                    results: items
                }
            },
            cache: true
        }
    })

    $('.tkt-upload-file').click(function (e) {
        e.preventDefault();
        let $this = $(this);

        let file = wp.media({
            multiple: false,
        }).open().on('select', function(){
            let uploaded = file.state().get('selection').first();
            let fileURL = uploaded.toJSON().url;
            $this.val(fileURL)

        });

    })

    $('.tkt-edit-date').click(function (e){
        e.preventDefault();

        let $this = $(this);
        $this.next('input').toggle();

    })

    $('.tkt-toggle-edit').click(function(e){
        e.preventDefault();

        let $this = $(this);

        $this.parentsUntil('#tkt-replies').next('.tkt-editor').slideToggle();
    })

})