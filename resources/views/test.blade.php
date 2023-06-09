<!DOCTYPE html>

<!-- HANYA FILE UNTUK TEST BOLEH DIUBAH / DIHAPUS -->

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    @vite('resources/sass/table-tree.scss')
</head>
<body>
    <div>
        <div id="jstree-table-header" class="d-flex align-items-center bg-light" style="height: 40px">
            <div class="flex-grow-1 fw-bolder text-uppercase" style="padding-left: 43px">Scope</div>
            <div class="fw-bolder text-uppercase" style="width: 200px">Status Generate</div>
            <div class="fw-bolder text-uppercase" style="width: 284px">Status Komponen Tagihan</div>
        </div>
        <div id="jstree_demo_div"></div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>

    <script>
        $(function () {
            initTree();

            $('#jstree_demo_div').on("loaded.jstree", function () {
                appendColumn();
            });

            $('#jstree_demo_div').on("before_open.jstree", function () {
                appendColumn();
            });
        });

        function appendColumn() {
            $('#jstree_demo_div .jstree-anchor').each(function() {
                if ($(this).children().length <= 2) {
                    const nodeId = $(this).parents('li').attr('id');
                    const node = $('#jstree_demo_div').jstree('get_node', nodeId);
                    console.log(node);
                    $(this).append(`<div style="flex-grow: 1; display: flex; justify-content: flex-end;">
                        <div style="width: 200px">${node.data.status_generated.text}</div>
                        <div style="width: 300px">Komponen tagihan sudah diset</div>
                    </div>`);
                }
            });
        }

        async function initTree() {
            const {data} = await $.ajax({
                async: true,
                url: 'http://localhost:8000/api/test',
                type: 'get',
            });

            $('#jstree_demo_div').jstree({
                'core' : {
                    'data' : data.tree,
                    "themes":{
                        "icons":false
                    }
                },
                "checkbox" : {
                    "keep_selected_style" : false
                },
                "plugins" : [ "checkbox", "wholerow" ],
            });
        }
    </script>
</body>
</html>
