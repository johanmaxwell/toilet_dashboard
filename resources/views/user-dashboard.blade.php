<x-dashboard title="User">
    <div class="container mt-4">
        <div class="d-flex justify-content-between mb-3">
            <h3>Daftar Company</h3>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCompanyModal">Add Company</button>
        </div>

        <div class="row" id="companyList"></div>
    </div>

    <!-- Modal Add Company-->
    <div class="modal fade" id="addCompanyModal" tabindex="-1" aria-labelledby="addCompanyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" id="addCompanyForm">
                <div class="modal-header">
                    <h5 class="modal-title">Add Company</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="companyName" class="form-label">Company Name</label>
                        <input type="text" class="form-control" id="companyName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Privacy</label>
                        <div>
                            <input type="radio" name="privacy" value="public" checked> Public
                            <input type="radio" name="privacy" value="private" class="ms-3"> Private
                        </div>
                    </div>
                    <div class="mb-3" id="kodeAksesContainer" style="display: none;">
                        <label for="kodeAkses" class="form-label">Kode Akses</label>
                        <input type="text" class="form-control" id="kodeAkses">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Add Admin-->
    <div class="modal fade" id="adminModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Admins</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="adminModalBody">
                    <!-- Dynamic content here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Topup-->
    <div class="modal fade" id="topupModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pilih Paket Top-Up</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-body ">
                                    <h5 class="card-title">Small <br> Plan</h5>
                                    <i class="fas fa-money-bill-wave fa-2x mb-2"></i>
                                    <p>200.000 dokumen</p>
                                    <p><strong>Rp15.000</strong></p>
                                    <button class="btn btn-primary buy-plan" data-plan="small">Beli</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Normal <br> Plan</h5>
                                    <i class="fas fa-money-bill-wave fa-2x mb-2"></i>
                                    <p>1.000.000 dokumen</p>
                                    <p><strong>Rp60.000</strong></p>
                                    <button class="btn btn-primary buy-plan" data-plan="normal">Beli</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Big <br> Plan</h5>
                                    <i class="fas fa-money-bill-wave fa-2x mb-2"></i>
                                    <p>5.000.000 dokumen</p>
                                    <p><strong>Rp250.000</strong></p>
                                    <button class="btn btn-primary buy-plan" data-plan="big">Beli</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @push('scripts')
        <script>
            const owner = localStorage.getItem('userEmail');

            function loadCompanies() {
                window.db.collection("company").where("owner", "==", owner).get()
                    .then(snapshot => {
                        $("#companyList").empty();

                        snapshot.forEach(async (doc) => {
                            const data = doc.data();
                            const companyId = doc.id;
                            const isDeactivated = data.is_deactivated === true;
                            const credit = data.credit || 0;

                            let totalReads = 0;
                            let totalWrites = 0;

                            try {
                                const usageSnapshot = await window.db.collection("usage_metrics").doc(companyId)
                                    .collection("daily").get();
                                usageSnapshot.forEach(logDoc => {
                                    const logData = logDoc.data();
                                    totalReads += logData.reads || 0;
                                    totalWrites += logData.writes || 0;
                                });
                            } catch (error) {
                                console.error(`Error loading usage_metrics for ${companyId}:`, error);
                            }

                            const currentCredit = credit - totalReads - totalWrites;
                            let cardClass = '';
                            if (isDeactivated) {
                                cardClass = 'bg-danger text-white';
                            } else if (currentCredit < 0) {
                                cardClass = 'bg-warning';
                            }

                            const html = `
            <div class="col-lg-6 col-md-12 mb-3" id="card-${companyId}">
                <div class="card h-100 shadow ${cardClass}">
                    <div class="card-body position-relative">
                        <div class="position-absolute top-0 end-0 p-2">
                            <button class="btn btn-sm btn-light ms-1 show-admins-btn" data-company-id="${companyId}" title="Manage Admins"><i class="fas fa-user"></i></button>
                            <button class="btn btn-sm btn-light me-1 edit-btn" data-company-id="${companyId}" title="Edit"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-light delete-btn" data-company-id="${companyId}" title="Delete"><i class="fas fa-trash"></i></button>
                        </div>
                        <div class="position-absolute bottom-0 end-0 p-2">
                            <button class="btn btn-sm btn-success topup-btn" data-company-id="${companyId }">
                                <i class="fas fa-money-bill-wave"></i> Top-Up
                                </button>
                        </div>
                        <div class="company-content">
                            <h3 class="card-title">${companyId}</h3>
                            ${isDeactivated ? '<p class="text-warning"><strong>Company Deactivated!</strong></p>' : ''}
                            ${currentCredit < 0 ? '<p class="text-danger"><strong>Credit Negative!</strong></p>' : ''}
                            <p>Privacy: <strong class="privacy-val">${data.privacy}</strong></p>
                            ${data.privacy === 'private' ? `<p class="kode-akses-field">Kode Akses: <strong class="kode-val">${data.kode_akses}</strong></p>` : ''}
                            <p>Credit Remaining: <strong>${currentCredit}</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        `;

                            $("#companyList").append(html);

                            // Edit button
                            $(`#card-${companyId} .edit-btn`).click(function() {
                                const card = $(`#card-${companyId}`);
                                const privacy = data.privacy;
                                const kode = data.kode_akses || '';

                                const editForm = `
        <h5 class="card-title">${companyId}</h5>
        ${isDeactivated ? '<p class="text-warning"><strong>Company Deactivated</strong></p>' : ''}
        <div class="mb-2">
            <label>Privacy:</label><br>
            <label><input type="radio" name="privacy-${companyId}" value="public" ${privacy === 'public' ? 'checked' : ''}> Public</label>
            <label class="ms-2"><input type="radio" name="privacy-${companyId}" value="private" ${privacy === 'private' ? 'checked' : ''}> Private</label>
        </div>
        <div class="mb-2 kode-input" style="${privacy === 'private' ? '' : 'display:none'}">
            <label>Kode Akses:</label>
            <input type="text" class="form-control" value="${kode}">
        </div>
        <p>Credit Remaining: <strong>${currentCredit}</strong></p>
    `;

                                card.find(".company-content").html(editForm);

                                // Toggle kode akses input
                                card.find(`input[name="privacy-${companyId}"]`).change(function() {
                                    const selected = $(this).val();
                                    card.find(".kode-input").toggle(selected === 'private');
                                });

                                // Change buttons to Save/Cancel
                                const btns = card.find(".edit-btn, .delete-btn");
                                btns.eq(0).html('<i class="fas fa-save"></i>').attr("title", "Save")
                                    .removeClass("edit-btn").addClass("save-btn");
                                btns.eq(1).html('<i class="fas fa-times"></i>').attr("title", "Cancel")
                                    .removeClass("delete-btn").addClass("cancel-btn");

                                // Save handler
                                btns.eq(0).off("click").on("click", async function() {
                                    const newPrivacy = card.find(
                                            `input[name="privacy-${companyId}"]:checked`)
                                        .val();
                                    const newKode = card.find(".kode-input input").val()
                                        .trim();

                                    const updateData = {
                                        privacy: newPrivacy
                                    };
                                    if (newPrivacy === "private") {
                                        updateData.kode_akses = newKode;
                                    } else {
                                        updateData.kode_akses = firebase.firestore
                                            .FieldValue
                                            .delete(); // remove if changed to public
                                    }

                                    const confirm = await Swal.fire({
                                        title: "Save Changes?",
                                        icon: "question",
                                        showCancelButton: true,
                                        confirmButtonText: "Save"
                                    });

                                    if (confirm.isConfirmed) {
                                        window.db.collection("company").doc(companyId)
                                            .update(updateData)
                                            .then(() => {
                                                Swal.fire("Updated!", "", "success");
                                                loadCompanies();
                                            })
                                            .catch(err => {
                                                console.error(err);
                                                Swal.fire("Error", "Failed to update",
                                                    "error");
                                            });
                                    }
                                });

                                // Cancel handler
                                btns.eq(1).off("click").on("click", function() {
                                    loadCompanies(); // reload to reset UI
                                });
                            });

                            // Delete handler
                            $(`#card-${companyId} .delete-btn`).click(async function() {
                                const confirm = await Swal.fire({
                                    title: "Delete this company?",
                                    text: `Company ID: ${companyId}`,
                                    icon: "warning",
                                    showCancelButton: true,
                                    confirmButtonText: "Delete"
                                });

                                if (confirm.isConfirmed) {
                                    window.db.collection("company").doc(companyId).delete()
                                        .then(() => {
                                            Swal.fire("Deleted!", "", "success");
                                            loadCompanies();
                                        })
                                        .catch(err => {
                                            console.error(err);
                                            Swal.fire("Error", "Failed to delete", "error");
                                        });
                                }
                            });

                        });
                    })
                    .catch(error => {
                        console.error("Error loading companies:", error);
                    });
            }

            $(document).ready(function() {
                loadCompanies();

                $('input[name="privacy"]').on('change', function() {
                    if ($(this).val() === "private") {
                        $("#kodeAksesContainer").show();
                    } else {
                        $("#kodeAksesContainer").hide();
                    }
                });

                $("#addCompanyForm").on("submit", async function(e) {
                    e.preventDefault();

                    const companyName = $.trim($("#companyName").val());
                    const privacy = $('input[name="privacy"]:checked').val();
                    const kode_akses = $.trim($("#kodeAkses").val());

                    const snapshot = await window.db.collection("company").get();
                    const duplicate = snapshot.docs.find(doc => doc.id.toLowerCase() === companyName
                        .toLowerCase());

                    if (duplicate) {
                        Swal.fire("Error", "Company name already exists!", "error");
                        return;
                    }

                    const newCompany = {
                        owner: owner,
                        privacy: privacy,
                        credit: 0,
                        date_added: firebase.firestore.Timestamp.now()
                    };
                    if (privacy === 'private') {
                        newCompany.kode_akses = kode_akses;
                    }

                    try {
                        await window.db.collection("company").doc(companyName).set(newCompany);
                        Swal.fire("Success", "Company added successfully", "success");
                        $("#addCompanyForm")[0].reset();
                        $("#kodeAksesContainer").hide();
                        const modal = bootstrap.Modal.getInstance(document.getElementById(
                            "addCompanyModal"));
                        modal.hide();
                        loadCompanies();
                    } catch (err) {
                        console.error(err);
                        Swal.fire("Error", "Something went wrong", "error");
                    }
                });
            });

            $(document).on("click", ".show-admins-btn", function() {
                const companyId = $(this).data("company-id");
                $("#adminModal").data("company-id", companyId);
                showAdminList(companyId);
                const modal = new bootstrap.Modal(document.getElementById("adminModal"));
                modal.show();
            });

            function showAdminList(companyId) {
                $("#adminModal .modal-title").text(`Daftar admin ${companyId}`);
                $("#adminModalBody").html("<p>Loading...</p>");

                window.db.collection("users").where("company", "==", companyId).where("role", "==", "admin").get()
                    .then(snapshot => {
                        let html = `<ul class="list-group mb-3">`;
                        snapshot.forEach(doc => {
                            const data = doc.data();
                            html += `<li class="list-group-item">${data.username}</li>`;
                        });
                        html += `</ul>
            <button class="btn btn-primary btn-sm" id="addAdminBtn"><i class="fas fa-plus"></i> Add Admin</button>`;
                        $("#adminModalBody").html(html);
                    })
                    .catch(err => {
                        console.error("Error fetching admins", err);
                        $("#adminModalBody").html("<p>Error loading admins.</p>");
                    });
            }

            $(document).on("click", "#addAdminBtn", function() {
                const formHtml = `
        <form id="addAdminForm">
            <div class="mb-3">
                <label>Username</label>
                <input type="text" class="form-control" id="newAdminUsername" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" class="form-control" id="newAdminPassword" required>
            </div>
            <button type="submit" class="btn btn-success">Submit</button>
            <button type="button" class="btn btn-secondary ms-2" id="cancelAddAdmin">Cancel</button>
        </form>
    `;
                $("#adminModalBody").html(formHtml);
            });

            $(document).on("click", "#cancelAddAdmin", function() {
                const companyId = $("#adminModal").data("company-id");
                showAdminList(companyId);
            });

            $(document).on("submit", "#addAdminForm", async function(e) {
                e.preventDefault();

                const username = $("#newAdminUsername").val().trim();
                const password = $("#newAdminPassword").val().trim();
                const companyId = $("#adminModal").data("company-id");

                if (!username || !password) {
                    Swal.fire("Error", "All fields are required.", "error");
                    return;
                }

                const hashedPassword = CryptoJS.SHA256(password).toString()

                window.db.collection("users").add({
                    username,
                    password: hashedPassword,
                    company: companyId,
                    role: 'admin',
                    date_added: firebase.firestore.Timestamp.now()
                }).then(() => {
                    Swal.fire("Success", "Admin added successfully", "success");
                    showAdminList(companyId);
                }).catch(err => {
                    console.error("Error adding admin:", err);
                    Swal.fire("Error", "Could not add admin.", "error");
                });
            });

            $(document).on('click', '.topup-btn', function() {
                const companyId = $(this).data('company-id');
                $('#topupModal').data('company-id', companyId).modal('show');
            });

            $(document).on('click', '.buy-plan', async function() {
                const plan = $(this).data('plan');
                const companyId = $('#topupModal').data('company-id');
                const email = localStorage.getItem('userEmail');

                const snapshot = await window.db.collection('account').where('email', '==', email).limit(1).get();
                const data = snapshot.docs[0].data();

                $.ajax({
                    url: '/topup',
                    method: 'POST',
                    data: {
                        plan: plan,
                        company_id: companyId,
                        first_name: data.first_name,
                        last_name: data.last_name,
                        phone: data.phone,
                        email: data.email,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function() {
                        alert('ke halaman bayar!');
                    },
                    error: function(xhr) {
                        alert('Terjadi kesalahan saat memproses top-up.');
                    }
                });
            });
        </script>
    @endpush
</x-dashboard>
