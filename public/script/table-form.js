$.fn.tableForm = function(params) {

    /* Variables and methods declaration */
    var currentId = 0;

    // Handling when user clicks add new row
    var tableFormAdd = function(event, obj) {

        // Disabling default event handling
        event.preventDefault();

        // Get current object's parent siblings length
        siblingLength = obj.parent()
            .parent()
            .siblings()
            .length;

        parentId = obj.parent()
            .parent()
            .attr('parent-id');

        if (parentId != '') {
            parentId = obj.parent()
                .parent()
                .attr('id');
        }

        // Add new row with condition
        if (siblingLength > 0) {
            generateNewRow({
                parentId: parentId
            }).hide()
                .insertAfter(obj
                    .parent()
                    .parent()
                    .siblings()
                    .last()
                ).fadeIn(1000);
        } else {
            generateNewRow({
                parentId: parentId
            }).hide()
                .insertAfter(obj
                    .parent()
                    .parent()
                ).fadeIn(1000);
        }

        // Remove add button
        obj.attr('class', 'btn btn-sm btn-danger')
            .unbind()
            .bind('click', {}, function(event) {

                event.preventDefault();
                var nodeName = obj.parent()
                    .parent()
                    .parent()
                    .parent()
                    .parent()
                    .prop('nodeName');

                if (nodeName == 'TABLE') {
                    var parentSiblings = obj.parent()
                        .parent()
                        .siblings();
                    $.each(parentSiblings, function(k, v) {
                        if($(this).first().children().length == 1) {
                            tableFormRemove($(this));
                        }
                    });
                    var removeObj = obj.parent()
                        .parent();
                } else {
                    var removeObj = obj.parent()
                        .parent()
                        .parent()
                        .parent()
                        .parent();
                }

                tableFormRemove(removeObj);
            })
            .find('i')
            .attr('class', 'fa fa-trash');

            refreshRatingInput();
    },
    // Generate a row for table header
    generateHeadRow = function() {

        // Initialize header row
        var headRow = $('<tr>');

        // If number is enabled, then display the number
        if (params.enableNumber) {

            // Default width = 5%
            headRow.append($('<td>', {
                style: 'width: 5%'
            }).html('No.'));
        }

        // Iterate each header column to be appended
        $.each(params.header, function(k, v) {

            // Insert label of header
            headRow.append($('<td>').html(k));
        });

        // If action is enabled, then display the action
        if (params.enableAction) {
            var actionLabel = $('<td>').html('Action');
            headRow.append(actionLabel);
        }

        return headRow;

    },

    // Generate new row of the table
    generateNewRow = function(data) {

        // Initialize a row
        currentId++;
        var row = $('<tr parent-id="' + data.parentId + '" id="row-' + currentId + '">');

        // If number is enabled, then display the number
        if (params.enableNumber) {

            var number = $('<td>').html(rowIterator);
            row.append(number);
        }

        // Iterate each column to be appended
        $.each(params.header, function(k, v) {
            // New field
            var idSplit = data.parentId.split(/\s*\-\s*/g);
            var input = $('<input>', v);

            switch (v.type) {
                case 'rating':
                    input.attr({
                        'type': 'number',
                        'class': 'rating',
                    });

                    type = 'rating';

                    refreshRatingInput();

                    break;

                case 'number':
                    input.attr({
                        'type': 'number'
                    });

                    type = 'value';

                    break;

                case 'text':
                    switch (v.name) {
                        case 'parameter[]':
                            type = 'parameter';
                            break;

                        case 'description[]':
                            type = 'description';
                            break;

                        default:
                            type = 'input';
                            break;
                    }

                    break;

                default:
                    type = 'input';
                    break;
            }

            input.attr({
                'parent-id': type + '-' + idSplit[1],
                'id': type + '-' + currentId,
                'name': type + '[row-' + currentId + ']',
            });

            // New column container
            col = $('<td>').append(input);

            // Append the column to a row
            row.append(col);
        });

        // If action or children is enabled
        if (params.enableAction || (params.children && params.children.enabled)) {
            var tdAction = $('<td>');

            // Create hidden input to identify parent
            var hiddenInput = $('<input>', {
                type: 'hidden',
                name: 'parent[row-' + currentId + ']',
                value: data.parentId
            });

            // Append it to the last column
            tdAction.append(hiddenInput);

            // If children is enabled
            if (params.children && params.children.enabled) {

                var button = $('<a href="#" class="btn btn-success btn-sm">');

                button.bind('click', {
                    params: params
                }, function(event) {
                    var grandParent = $(this).parent()
                        .parent();
                    var parentSiblingsLength = $(this).parent()
                        .siblings()
                        .length;
                    addChildrenRow(event,
                        grandParent,
                        parentSiblingsLength, {
                            parentId: data.parentId
                        });
                });

                var icon = $('<i>', {
                    class: 'fa fa-arrow-down'
                });

                button.prepend(icon);
                tdAction.append(button)
            }

            // If action is enabled, then display the action
            if (params.enableAction) {

                var button = $('<a href="#" class="btn btn-primary btn-sm">');

                button.bind('click', {
                    params: params
                }, function(event) {
                    tableFormAdd(event, $(this));
                });

                var icon = $('<i>', {
                    class: 'fa fa-plus-circle'
                });

                button.prepend(icon);
                tdAction.append(button)
            }

            row.append(tdAction);
        }

        rowIterator++;

        return row;
    },

    tableFormRemove = function(obj) {
        obj.remove();
    },

    addChildrenRow = function(event, obj, span, data) {
        event.preventDefault();

        var parentId = obj.attr('id');
        var rowChild = $('<tr>');
        var tableChild = $('<table class="table">').append(generateNewRow({
            parentId: parentId
        }));
        var col2 = $('<td colspan="' + (span + 1) + '">').append(tableChild);

        rowChild.append(col2);
        rowChild.insertAfter(obj);

        refreshRatingInput();
    },
    refreshRatingInput = function() {
        $('input.rating[type=number]').each(function() {
            $(this).rating();
        });
    },

    // Initialize table
    table = $('<table>', {
        class: params.class ? params.class : 'table'
    }),

    // Initialize table head
    head = $('<thead>'),

    // Initialize table body
    body = $('<tbody>'),

    // Initialize number iterator
    rowIterator = 1;

    // Generate initial tabel header
    head.append(generateHeadRow());
    table.append(head);

    // Generate initial tabel body
    body.append(generateNewRow({
        parentId: ''
    }));
    table.append(body);

    $(this).append(table);
};
