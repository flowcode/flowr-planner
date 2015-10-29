        var $collectionHolder;
        var $addTagLink = $(tagAAddOther);
        jQuery(document).ready(function() {
            $collectionHolder = $('#contentReminders');
            $collectionHolder.append($addTagLink);
            $collectionHolder.data('index', $collectionHolder.find(':input').length);
            $collectionHolder.find('.reminders').each(function() {
                addReminderFormDeleteLink($(this));
            });
            $addTagLink.on('click', function(e) {
                e.preventDefault();
                addReminderForm($collectionHolder, $addTagLink);
            });
        });
        function addReminderForm($collectionHolder, $newLink) {
            var prototype = $("#reminderTemplate");
            var index = $collectionHolder.data('index');
            var newForm = prototype.html().replace(/__name__/g, index);
            newForm = $(newForm);
            $collectionHolder.data('index', index + 1);
            $newLink.before(newForm);
            addReminderFormDeleteLink(newForm);
        }

        function addReminderFormDeleteLink($tagFormLi) {
            var $removeFormA = $(tagARemove);
            $tagFormLi.find(".tools").append($removeFormA);
            $removeFormA.on('click', function(e) {
                e.preventDefault();
                $tagFormLi.remove();
            });
        }
