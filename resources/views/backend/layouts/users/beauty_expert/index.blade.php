@extends('backend.app')

@section('title', 'Beauty Expert Users List')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">All User List</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="datatable"
                                    class="table table-bordered dt-responsive nowrap table-striped align-middle"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="column-id">#</th>
                                            <th class="column-content">Name</th>
                                            <th class="column-content">Email</th>
                                            <th class="column-content">Status</th>
                                            <th class="column-content text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Dynamic Data --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal for Viewing User Details --}}
    <div class="modal fade" id="viewUserModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">User Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <a id="businessAvatarLink" href="#" target="_blank">
                                <img id="businessAvatar" src="" class="img-thumbnail rounded-circle" width="120">
                            </a>
                        </div>

                        <div class="col-md-8">
                            <h5 id="userName" class="fw-bold"></h5>
                            <p><i class="fas fa-envelope"></i> <span id="userEmail"></span></p>
                            <p><i class="fas fa-phone"></i> <span id="userPhoneNumber"></span></p>
                            <p><i class="fas fa-user-tag"></i> <span id="userRole" class="badge bg-info"></span></p>
                            <p><i class="fas fa-toggle-on"></i> <span id="userStatus" class="badge bg-success"></span></p>
                        </div>
                    </div>
                    <hr>
                    <h6>Business Information</h6>
                    <p><strong>Business Name:</strong> <span id="businessName"></span></p>
                    <p><strong>Address:</strong> <span id="businessAddress"></span></p>
                    <p><strong>Bio:</strong> <span id="businessBio"></span></p>
                    <p><strong>License:</strong> <a id="businessLicense" href="#" target="_blank">View License</a></p>
                    <hr>
                    <h6>Services</h6>
                    <ul id="serviceList"></ul>
                    <hr>
                    <h6>Travel Radius</h6>
                    <p><strong>Free Radius:</strong> <span id="freeRadius"></span> km</p>
                    <p><strong>Max Radius:</strong> <span id="maxRadius"></span> km</p>
                    <p><strong>Travel Charge:</strong> $<span id="travelCharge"></span></p>
                    <p><strong>Max Charge:</strong> $<span id="maxCharge"></span></p>
                    <p><strong>Min Booking Value:</strong> $<span id="minBookingValue"></span></p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });

            if (!$.fn.DataTable.isDataTable('#datatable')) {
                var table = $('#datatable').DataTable({
                    responsive: true,
                    order: [],
                    lengthMenu: [
                        [10, 25, 50, 100, -1],
                        [10, 25, 50, 100, "All"],
                    ],
                    processing: true,
                    serverSide: true,
                    pagingType: "full_numbers",
                    ajax: {
                        url: "{{ route('user.beauty-expert.index') }}",
                        type: "GET",
                    },
                    dom: "<'row table-topbar'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>>" +
                        "<'row'<'col-12'tr>>" +
                        "<'row table-bottom'<'col-md-5 dataTables_left'i><'col-md-7'p>>",
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Search records...",
                        lengthMenu: "Show _MENU_ entries",
                        processing: `
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>`,
                    },
                    // Turn off autoWidth so column widths are respected.
                    autoWidth: false,
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            width: '5%'
                        },
                        {
                            data: 'name',
                            name: 'name',
                            orderable: true,
                            searchable: true,
                            width: '30%',
                            render: function(data) {
                                return '<div style="white-space:normal;word-break:break-word;">' +
                                    data + '</div>';
                            }
                        },
                        {
                            data: 'email',
                            name: 'email',
                            orderable: true,
                            searchable: true,
                            width: '40%',
                            render: function(data) {
                                return '<div style="white-space:normal;word-break:break-word;">' +
                                    data + '</div>';
                            }
                        },
                        {
                            data: 'status',
                            name: 'status',
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            width: '20%'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            width: '5%'
                        },
                    ],
                });

                dTable.buttons().container().appendTo('#file_exports');
                new DataTable('#example', {
                    responsive: true
                });
            }
        });

        // Status Change
        function changeStatus(id, newStatus, selectEl) {
            const $select = $(selectEl);
            const oldStatus = $select.val(); // remember what was selected
            const $cell = $select.parent(); // where we’ll attach the spinner

            // disable so user can’t click again
            $select.prop('disabled', true);

            // add a tiny bootstrap spinner
            const $spin = $(`<div class="spinner-border spinner-border-sm ms-2" role="status">
                    <span class="visually-hidden">Loading...</span>
                  </div>`);
            $cell.append($spin);

            // fire the request
            axios.post(
                    '{{ route('user.beauty-expert.status', ['id' => ':id']) }}'.replace(':id', id), {
                        status: newStatus,
                        _token: '{{ csrf_token() }}'
                    }
                )
                .then(resp => {
                    if (resp.data.success) {
                        toastr.success(resp.data.message);
                        // make sure select actually shows what the DB says
                        $select.val(resp.data.data.status);
                    } else {
                        toastr.error(resp.data.message || 'Invalid status');
                        // revert on logical failure
                        $select.val(oldStatus);
                    }
                })
                .catch(err => {
                    toastr.error('An error occurred. Please try again.');
                    $select.val(oldStatus);
                })
                .finally(() => {
                    // cleanup
                    $spin.remove();
                    $select.prop('disabled', false);
                });
        }

        // delete Confirm
        function showDeleteConfirm(id) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure you want to delete this record?',
                text: 'If you delete this, it will be gone forever.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteItem(id);
                }
            });
        }

        // Delete Button
        function deleteItem(id) {
            let url = '{{ route('user.beauty-expert.destroy', ['id' => '__id__']) }}'.replace('__id__', id);
            let csrfToken = '{{ csrf_token() }}';
            $.ajax({
                type: "DELETE",
                url: url.replace(':id', id),
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(resp) {
                    $('#datatable').DataTable().ajax.reload();
                    if (resp['t-success']) {
                        toastr.success(resp.message);
                    } else {
                        toastr.error(resp.message);
                    }
                },
                error: function(error) {
                    toastr.error('An error occurred. Please try again.');
                }
            });
        }

        // Fetch and display user details
        function showUserDetails(id) {
            let url = '{{ route('user.beauty-expert.show', ['id' => '__id__']) }}'.replace('__id__', id);
            url = url.replace(':id', id);

            axios.get(url)
                .then(function(response) {
                    let data = response.data;

                    // Basic user info
                    $('#userName').text(data.name);
                    $('#userEmail').text(data.email);
                    $('#userPhoneNumber').text(data.phone_number ? data.phone_number : 'N/A');
                    $('#userRole').text(data.role);
                    $('#userStatus').text(data.status);

                    // Business information
                    if (data.business_info) {
                        $('#businessName').text(data.business_info.business_name);
                        $('#businessAddress').text(data.business_info.business_address);
                        $('#businessBio').text(data.business_info.bio);

                        // Avatar link and image
                        if (data.business_info.avatar) {
                            $('#businessAvatarLink').attr('href', data.business_info.avatar).attr('target', '_blank');
                            $('#businessAvatar').attr('src', data.business_info.avatar);
                        } else {
                            $('#businessAvatarLink').attr('href', '#');
                            $('#businessAvatar').attr('src', '');
                        }

                        // License link
                        if (data.business_info.license) {
                            $('#businessLicense')
                                .attr('href', data.business_info.license)
                                .attr('target', '_blank')
                                .removeAttr('download', '')
                                .text('View License');
                        } else {
                            $('#businessLicense')
                                .attr('href', '#')
                                .text('No License Available');
                        }
                    } else {
                        $('#businessName, #businessAddress, #businessBio').text('N/A');
                        $('#businessAvatarLink').attr('href', '#');
                        $('#businessAvatar').attr('src', '');
                        $('#businessLicense').attr('href', '#').text('No License');
                    }

                    // Services
                    let serviceList = $('#serviceList');
                    serviceList.empty();
                    if (data.services.length > 0) {
                        data.services.forEach(service => {
                            let serviceItem = `
                            <li>
                                <strong>${service.service_name}</strong><br>
                                Offered Price: $${service.offered_price}, Total Price: $${service.total_price}<br>
                                ${
                                    service.image
                                        ? `<a href="${service.image}" target="_blank">
                                                                <img src="${service.image}" class="img-thumbnail mt-1" width="80">
                                                            </a>`
                                        : ''
                                }
                            </li>
                        `;
                            serviceList.append(serviceItem);
                        });
                    } else {
                        serviceList.append('<li>No services available</li>');
                    }

                    // Travel radius
                    if (data.travel_radius) {
                        $('#freeRadius').text(data.travel_radius.free_radius);
                        $('#maxRadius').text(data.travel_radius.max_radius);
                        $('#travelCharge').text(data.travel_radius.travel_charge);
                        $('#maxCharge').text(data.travel_radius.max_charge);
                        $('#minBookingValue').text(data.travel_radius.min_booking_value);
                    } else {
                        $('#freeRadius, #maxRadius, #travelCharge, #maxCharge, #minBookingValue').text('N/A');
                    }

                    // Finally, show the modal
                    $('#viewUserModal').modal('show');
                })
                .catch(function(error) {
                    console.error(error);
                    toastr.error('Could not fetch user details.');
                });
        }
    </script>
@endpush
