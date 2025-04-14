<!-- BEGIN: Vendor JS-->
<script src="{{ asset(mix('assets/vendor/libs/jquery/jquery.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/popper/popper.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/js/bootstrap.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/node-waves/node-waves.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/hammer/hammer.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/typeahead-js/typeahead.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/js/menu.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/flatpickr/flatpickr.js')) }}"></script>

<script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.min.js"></script>

<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>

<script src="https://cdn.datatables.net/buttons/3.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.2/js/buttons.bootstrap5.min.js"></script>

<script src="https://cdn.datatables.net/buttons/3.2.2/js/buttons.colVis.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.2/js/buttons.html5.min.js"></script>

<!-- Include jQuery Validation -->
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<!-- Include jQuery Validation additional methods (optional) -->
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
@yield('vendor-script')
<!-- END: Page Vendor JS-->
<!-- BEGIN: Theme JS-->
<script src="{{ asset(mix('assets/js/main.js')) }}"></script>

<!-- END: Theme JS-->
<script>
    function toggleSearchBox() {
        var biosSearchInput = document.getElementById('bios-search-input');
        if (biosSearchInput) { // Check if biosSearchInput exists
            if (biosSearchInput.classList.contains('open')) {
                closeSearchBox();
            } else {
                openSearchBox();
            }
        }
    }

    function openSearchBox() {
        var biosSearchInput = document.getElementById('bios-search-input');
        var biosSearchIcon = document.getElementById('bios-search-icon');

        if (biosSearchInput && biosSearchIcon) { // Check if both elements exist
            biosSearchInput.classList.add('open');
            biosSearchInput.style.width = '200px';
            biosSearchInput.style.padding = '5px 10px';
            biosSearchInput.style.opacity = '1';
            biosSearchInput.focus();
            biosSearchIcon.classList.add('d-none');
        }
    }

    function closeSearchBox() {
        var biosSearchInput = document.getElementById('bios-search-input');
        var biosSearchIcon = document.getElementById('bios-search-icon');

        if (biosSearchInput && biosSearchIcon) { // Check if both elements exist
            biosSearchInput.classList.remove('open');
            biosSearchInput.style.width = '0';
            biosSearchInput.style.padding = '0';
            biosSearchInput.style.opacity = '0';
            biosSearchIcon.classList.remove('d-none');
        }
    }

    // Close the search box if clicking outside of it
    document.addEventListener('click', function(event) {
        var biosSearchContainer = document.getElementById('bios-search-container');
        var biosSearchInput = document.getElementById('bios-search-input');

        if (biosSearchContainer && biosSearchInput) { // Check if both elements exist
            if (!biosSearchContainer.contains(event.target) && biosSearchInput.classList.contains('open')) {
                closeSearchBox();
            }
        }
    });

    // Close the search box if focus is lost
    var biosSearchInputElement = document.getElementById('bios-search-input');
    if (biosSearchInputElement) { // Check if biosSearchInput exists
        biosSearchInputElement.addEventListener('blur', function(event) {
            setTimeout(function() { // Set a timeout to ensure this runs after a potential click inside the search box
                if (document.activeElement !== biosSearchInputElement) {
                    closeSearchBox();
                }
            }, 100);
        });
    }

    $(document).ready(function () {
    $('.favourite-btn').on('click', function () {
        var button = $(this);
        var menuItemSlug = button.data('id');
        var menuItemName = button.data('name');
        var menuItemUrl = button.data('url');
        var menuItemIcon = button.data('icon');
        var isFavourite = button.data('favourite');

        $.ajax({
            url: '/manageMenuFavourites',
            method: 'POST',
            data: { menuItemSlug, menuItemName, menuItemUrl, menuItemIcon, isFavourite },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (response) {
                if (response.success) {
                    var icon = button.find('i');
                    if (isFavourite) {
                        // Change to outlined heart (not favourite)
                        icon.removeClass('ti-heart-filled text-danger').addClass('ti-heart');
                        button.data('favourite', 'false');
                    } else {
                        // Change to filled heart (favourite) and red color
                        icon.removeClass('ti-heart').addClass('ti-heart-filled text-danger');
                        button.data('favourite', 'true');
                    }

                    // refresh the page after success
                    location.reload();
                } else {
                    console.log('An error occurred. Please try again.');
                }
            },
            error: function (xhr, status, error) {
                console.log('Error: ' + error);
            }
        });
    });

    flatpickr(".time24", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        allowInput: true,
        minuteIncrement: 1
    });
});
</script>
<!-- BEGIN: Page JS-->
@yield('page-script')
<!-- END: Page JS-->