/**
 *  Match Omekas pagination styles.
 *
 *  This is based of the [Input Plugin](https://github.com/DataTables/Plugins/blob/master/pagination/input.js) created
 *  by [Allan Jardine](http://sprymedia.co.uk)
 *
 *  @example
 *    $(document).ready(function() {
 *        $('#example').dataTable( {
 *            "pagingType": "input"
 *        } );
 *    } );
 */

(function ($) {
    function calcDisableClasses(oSettings) {
        var start = oSettings._iDisplayStart;
        var length = oSettings._iDisplayLength;
        var visibleRecords = oSettings.fnRecordsDisplay();
        var all = length === -1;

        // Gordey Doronin: Re-used this code from main jQuery.dataTables source code. To be consistent.
        var page = all ? 0 : Math.ceil(start / length);
        var pages = all ? 1 : Math.ceil(visibleRecords / length);

        var disableFirstPrevClass = (page > 0 ? '' : 'visually-hidden');
        var disableNextLastClass = (page < pages - 1 ? '' : 'visually-hidden');

        return {
            'previous': disableFirstPrevClass,
            'next': disableNextLastClass,
        };
    }

    function calcCurrentPage(oSettings) {
        return Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength) + 1;
    }

    function calcPages(oSettings) {
        return Math.ceil(oSettings.fnRecordsDisplay() / oSettings._iDisplayLength);
    }

    var previousClassName = 'previous';
    var nextClassName = 'next';

    var paginateClassName = 'paginate';

    $.fn.dataTableExt.oPagination.input = {
        'fnInit': function (oSettings, nPaging, fnCallbackDraw) {
            var nPaginationWrapper = document.createElement('ul');
            nPaginationWrapper.className = 'pagination';
            var nPreviousWrapper = document.createElement('li');
            nPreviousWrapper.className = 'pagination_previous';
            var nPrevious = document.createElement('a');
            var nNextWrapper = document.createElement('li');
            nNextWrapper.className = 'pagination_next';
            var nNext = document.createElement('a');
            var nInputWrapper = document.createElement('li');
            nInputWrapper.className = 'page-input';
            var nInput = document.createElement('input');
            var nOf = document.createElement('span');

            nPrevious.innerHTML = 'Previous Page';
            nNext.innerHTML = 'Next Page';
            nOf.innerHTML = 'of ';
            nOf.className = 'pagination_current';

            if (oSettings.sTableId !== '') {
                nPaging.setAttribute('id', oSettings.sTableId + '_' + paginateClassName);
                nPrevious.setAttribute('id', oSettings.sTableId + '_' + previousClassName);
                nNext.setAttribute('id', oSettings.sTableId + '_' + nextClassName);
            }

            nInput.type = 'text';

            nPreviousWrapper.appendChild(nPrevious);
            nNextWrapper.appendChild(nNext);
            nInputWrapper.appendChild(nInput);
            nInputWrapper.appendChild(nOf);

            nPaginationWrapper.appendChild(nPreviousWrapper);
            nPaginationWrapper.appendChild(nInputWrapper);
            nPaginationWrapper.appendChild(nNextWrapper);


            nPaging.appendChild(nPaginationWrapper);

            $(nPrevious).click(function() {
                var iCurrentPage = calcCurrentPage(oSettings);
                if (iCurrentPage !== 1) {
                    oSettings.oApi._fnPageChange(oSettings, 'previous');
                    fnCallbackDraw(oSettings);
                }
            });

            $(nNext).click(function() {
                var iCurrentPage = calcCurrentPage(oSettings);
                if (iCurrentPage !== calcPages(oSettings)) {
                    oSettings.oApi._fnPageChange(oSettings, 'next');
                    fnCallbackDraw(oSettings);
                }
            });

            $(nInput).keyup(function (e) {
                // 38 = up arrow, 39 = right arrow
                if (e.which === 38 || e.which === 39) {
                    this.value++;
                }
                // 37 = left arrow, 40 = down arrow
                else if ((e.which === 37 || e.which === 40) && this.value > 1) {
                    this.value--;
                }

                if (this.value === '' || this.value.match(/[^0-9]/)) {
                    /* Nothing entered or non-numeric character */
                    this.value = this.value.replace(/[^\d]/g, ''); // don't even allow anything but digits
                    return;
                }

                var iNewStart = oSettings._iDisplayLength * (this.value - 1);
                if (iNewStart < 0) {
                    iNewStart = 0;
                }
                if (iNewStart >= oSettings.fnRecordsDisplay()) {
                    iNewStart = (Math.ceil((oSettings.fnRecordsDisplay() - 1) / oSettings._iDisplayLength) - 1) * oSettings._iDisplayLength;
                }

                oSettings._iDisplayStart = iNewStart;
                fnCallbackDraw(oSettings);
            });

            // Take the brutal approach to cancelling text selection.
            $('span', nPaging).bind('mousedown', function () { return false; });
            $('span', nPaging).bind('selectstart', function() { return false; });

            // If we can't page anyway, might as well not show it.
            var iPages = calcPages(oSettings);
            if (iPages <= 1) {
                $(nPaging).hide();
            }
        },

        'fnUpdate': function (oSettings) {
            if (!oSettings.aanFeatures.p) {
                return;
            }

            var iPages = calcPages(oSettings);
            var iCurrentPage = calcCurrentPage(oSettings);

            var an = oSettings.aanFeatures.p;
            if (iPages <= 1) // hide paging when we can't page
            {
                $(an).hide();
                return;
            }

            var disableClasses = calcDisableClasses(oSettings);

            $(an).show(oSettings.oClasses.sPageButtonDisabled);

            // Enable/Disable `prev` button.
            $(an).find('.pagination_previous')
                .removeClass('visually-hidden')
                .addClass(disableClasses['previous']);

            // Enable/Disable `next` button.
            $(an).find('.pagination_next')
                .removeClass('visually-hidden')
                .addClass(disableClasses['next']);

            // Paginate of N pages text
            $(an).find('.pagination_current').html(' of ' + iPages);

            // Current page number input value
            $(an).find('.page-input > input').val(iCurrentPage);
        }
    };
})(jQuery);