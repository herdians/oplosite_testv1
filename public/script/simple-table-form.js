$.fn.tableForm = function(params) {

    /* Variables and methods declaration */

    // Handling when user clicks add new row
    var tableFormAdd = function(event, obj) {

        // Disabling default event handling
        event.preventDefault();

        // Add new row
        generateNewRow().hide().appendTo(table).fadeIn(1000);

        // Remove add button
        obj.attr('class', 'btn btn-sm btn-danger')
            .unbind()
            .bind('click', {}, function(event) {

                event.preventDefault();
                tableFormRemove(obj.parent().parent());
            })
            .find('i')
            .attr('class', 'fa fa-trash');
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
    generateNewRow = function() {

        // Initialize a row
        var row = $('<tr>');

        // If number is enabled, then display the number
        if (params.enableNumber) {

            var number = $('<td>').html(rowIterator);
            row.append(number);
        }

        // Iterate each column to be appended
        $.each(params.header, function(k, v) {

            // New field
            var input = $('<input>', v);
            if (v.type == 'rating') {
                input.attr({
                    'type': 'number',
                    'class': 'rating',
                });

                $('input.rating[type=number]').each(function() {
                    $(this).rating();
                });
            }

            // New column container
            col = $('<td>').append(input);

            // Append the column to a row
            row.append(col);
        });

        // If action is enabled, then display the action
        if (params.enableAction) {

            var button = $('<button>', {
                class: 'btn btn-primary btn-sm'
            });

            button.bind('click', {
                params: params
            }, function(event) {

                tableFormAdd(event, $(this));
            });

            var icon = $('<i>', {
                class: 'fa fa-plus-circle'
            });

            button.prepend(icon);

            row.append(
                $('<td>').append(button)
            );
        }

        rowIterator++;

        return row;
    },
    tableFormRemove = function(obj) {

        obj.remove();
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
    body.append(generateNewRow());
    table.append(body);

    $(this).append(table);
};
