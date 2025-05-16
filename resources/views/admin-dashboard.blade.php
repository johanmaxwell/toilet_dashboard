<x-dashboard title="Admin">
    <h3>Company Data</h3>
    <div class="table-responsive">
        <table id="companyTable" class="display table table-striped table-bordered" style="width:100%">
            <thead>
                <tr class="bg-body-secondary">
                    <th class="text-center">Company</th>
                    <th class="text-center">Owner</th>
                    <th class="text-center">Privacy</th>
                    <th class="text-center">Reads</th>
                    <th class="text-center">Writes</th>
                    <th class="text-center">Total</th>
                    <th class="text-center">Credit</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    @push('scripts')
        <script>
            let table;

            $(document).ready(function() {
                table = $('#companyTable').DataTable();
                loadCompanyData();
            });

            async function loadCompanyData() {
                try {
                    const snapshot = await window.db.collection('company').get();
                    const companies = [];

                    for (const doc of snapshot.docs) {
                        const data = doc.data();
                        let sumRead = 0;
                        let sumWrite = 0;

                        const rwSnapshot = await window.db
                            .collection('usage_metrics')
                            .doc(doc.id)
                            .collection('daily')
                            .get();

                        rwSnapshot.forEach((rwDoc) => {
                            const rwData = rwDoc.data();
                            sumRead += rwData.reads || 0;
                            sumWrite += rwData.writes || 0;
                        });

                        const total = sumRead + sumWrite;

                        const companyDoc = await window.db.collection('company').doc(doc.id).get();
                        const credit = companyDoc.data().credit || 0;
                        const adjustedCredit = credit - total;
                        const isDeactivated = companyDoc.data().is_deactivated || false;

                        const actionButton = isDeactivated ?
                            `<button class="btn btn-primary w-100" onclick="toggleDeactivation('${doc.id}', false)">Activate</button>` :
                            `<button class="btn btn-danger w-100" onclick="toggleDeactivation('${doc.id}', true)">Deactivate</button>`;

                        companies.push([
                            doc.id,
                            data.owner || 'N/A',
                            data.privacy || 'N/A',
                            sumRead,
                            sumWrite,
                            total,
                            formatWithDots(adjustedCredit),
                            actionButton
                        ]);
                    }

                    table.clear().rows.add(companies).draw(false);
                } catch (error) {
                    console.error('Error loading company data:', error);
                }
            }

            async function toggleDeactivation(companyId, deactivate) {
                const action = deactivate ? 'deactivate' : 'activate';
                const result = await Swal.fire({
                    title: `Confirm ${action}`,
                    text: `Are you sure you want to ${action} this company?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, confirm',
                    cancelButtonText: 'Cancel',
                });

                if (result.isConfirmed) {
                    try {
                        await window.db.collection('company').doc(companyId).update({
                            is_deactivated: deactivate
                        });

                        await loadCompanyData();

                        Swal.fire(`Success!`,
                            `The company has been ${action}d.`, 'success');
                    } catch (error) {
                        console.error(`Error ${action}ing company:`, error);
                        Swal.fire('Error', `Failed to ${action} company.`, 'error');
                    }
                }
            }

            function formatWithDots(number) {
                return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }
        </script>
    @endpush
</x-dashboard>
