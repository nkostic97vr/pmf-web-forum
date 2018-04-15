$(function () {
    $(".dd").nestable({
        maxDepth: 2,
        scroll: false
    });

    $(".sortable-categories").sortable({
        scroll: false,
        handle: ".category-header",
        connectWith: ".sortable-categories"
    });

    $(".forums-tree-controls").on("click", function (e) {
        const action = $(e.target).text();
        if (isEqualToAnyWord("- +", action)) {
            // we are interested in a particular category
            const dd = $(this).parents(".category-header").siblings(".dd");
            if (action === "+") {
                dd.nestable("expandAll");
            } else if (action === "-") {
                dd.nestable("collapseAll");
            }
        }
    });

    $(".categories-tree-controls").on("click", function (e) {
        const action = $(e.target).text();
        if (isEqualToAnyWord("- +", action)) {
            // we are interested in all categories
            const dds = $(".dd");
            const btns = $(".category-tree-control");
            if (action === "-") {
                dds.hide();
                btns.attr("data-action", "");
            } else if (action === "+") {
                dds.show();
                btns.attr("data-action", "collapse");
            }
        }
    });

    $(".category-tree-control").on("click", function () {
        const dds = $(this).parents(".category-header").siblings(".dd");
        if ($(this).attr("data-action") === "collapse") {
            dds.hide();
            $(this).attr("data-action", "");
        } else {
            dds.show();
            $(this).attr("data-action", "collapse");
        }
    });

    $("button[name=save]").on("click", function () {
        const data = {};

        let position = 1;
        $(".dd").each(function () {
            data[$(this).data("categoryid")] = {
                position: position++,
                forums: $(this).nestable("serialize")
            };
        });

        $.post(route("ajax.positions"), { data }, function () {
            toastr.success('toastr.success');
        }).fail(function () {
            toastr.error('toastr.error');
        });
    });
});
