/**
 * Removing a link between a character and a database
 * @param {integer} guild_id 
 * @param {integer} character_id 
 * @param {string} context Node ID
 */
function remove_link(guild_id, character_id, context = '') {
    $.ajax({
        url: `/api/linked_character/${guild_id}/${character_id}`,
        method: 'delete',
        context: document.getElementById(context),
        success: function () {
            console.log('Removed')
            $(this).closest('tr').remove()
        }
    })
}



function toggle_tracking(guild_id, character_id, context = '') {

    $.get({

        // Url
        url: `/api/tracking/${guild_id}/${character_id}`,

        // If data is retrieved
        success: function () {
            console.log('Remove pending')
            $.ajax({
                url: `/api/tracking/${guild_id}/${character_id}`,
                method: 'delete',
                context: document.getElementById(context),
                success: function () {
                    console.log('Removed')
                    $(this).text('No')
                }
            })
        },

        // If Data is not retrieved
        error: function () {
            console.log('Adding pending')
            $.post({
                url: '/api/tracking',
                context: document.getElementById(context),
                data: {
                    'character_id': character_id,
                    'guild_id': guild_id
                },
                success: function () {
                    console.log('Added')
                    $(this).text('Yes')
                }
            })
        }
    })
}
