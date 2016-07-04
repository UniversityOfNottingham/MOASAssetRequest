(function(w, d, $) {

    var assetRequest = {

        initialise: function () {
            var self = this;

            // setup clicking on the 'use' button
            $('.js-use').on('click', function() {
                self._toggleModal('open');
            });

            // setup clicking on the close button or hitting escape
            $('.js-close-modal').on('click', function() {
                self._toggleModal('close');
            });
            $(document).keyup(function(e) {
                if (e.keyCode == 27) { // esc
                    self._toggleModal('close');
                }
            });

            // stop clicks on modal dialog from closing the dialog
            $('.js-modal-content').on('click', function(e) {
                e.stopPropagation();
            });

            $('#use-resource-form').submit(function(e) {
                e.preventDefault();
                self.submitRequest();
            });

            this._setupHashDetection();
            this._setupTermsAndConditions();
        },

        submitRequest: function() {
            var formErrors = !!($('#requester_name').val() === '' || $('#requester_org').val() === '' || $('#requester_org_type option:selected').val() === '0'),
                acceptTerms = ($('#accept_terms').prop('checked')) ? true : false;

            if (formErrors) {
                $('.js-form-error').slideDown(200, 'easeInOutCubic');
            }

            if (!acceptTerms) {
                $('.js-terms-error').slideDown(200, 'easeInOutCubic');
            }

            if (formErrors || !acceptTerms) return;

            var formData = $(this).serialize();
            console.log(formData);
        },

        _setupHashDetection: function() {
            var self = this;

            if(window.location.hash === '#use-this-resource') {
                this._toggleModal('open');
            }
            $(window).on('hashchange', function() {
                if(window.location.hash == '#use-this-resource') {
                    self._toggleModal('open');
                }
                else {
                    self._toggleModal('close');
                }
            });
        },

        _setupTermsAndConditions: function() {
            $('.js-show-terms').on('click', function(e) {
                e.preventDefault();
                $('.js-terms').slideToggle(200, 'easeInOutCubic');
                return false;
            });
        },

        _toggleModal: function(action) {
            if(action === 'open') {
                $('body').addClass('modal-open');
                window.location.hash = '#use-this-resource';
            }
            else if(action === 'close') {
                $('body').removeClass('modal-open');
                window.location.hash = '';
                $('#use-resource-form')[0].reset();
            }
        },
    };

    $(assetRequest.initialise());

}(window, document, window.jQuery));
