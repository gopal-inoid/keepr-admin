@extends('layouts.back-end.app')

@push('css_or_js')
    <style>
        .add-product-faq-btn,
        .remove-product-faq-btn {
            font-size: 25px;
            cursor: pointer;
        }

        .faq-add-main-btn {
            align-items: center;
            display: flex;
            flex-direction: row;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center">
                <img class="mr-2" src="{{ asset('/assets/back-end/img/Tax_Solid.svg') }}" alt="">
                {{ \App\CPU\translate('tax_calculations_edit') }}
            </h2>
        </div>
        <!-- End Page Title -->
        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <form class="update_tax"
                        action="{{ route('admin.business-settings.shipping-method.tax-calculation-update', [$tax_data['id']]) }}"
                        method="post">
                        @csrf
                        @method('put')
                        <div class="card-header">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="title">{{ \App\CPU\translate('country') }}</label>
                                    <input type="text" name="country" class="form-control asdf"
                                        value="{{ $tax_data['country'] }}" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive pb-3">
                                <table
                                    class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                                    cellspacing="0">
                                    <thead class="thead-light thead-50 text-capitalize">
                                        <tr>
                                            <th>States</th>
                                            <th>Tax Percentage</th>
                                            <th>Tax Type</th>
                                            <th>Tax Percentage</th>
                                            <th>Tax Type</th>
                                            <th>
                                                <div class="faq-add-main-btn">
                                                    <label class="title-color">&nbsp;</label>
                                                    <i
                                                        class="tio-add-circle-outlined text-success add-product-faq-btn mt-3"></i>
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="parent-faq-div">
                                        @if (!empty($tx_amt))
                                            @foreach ($tx_amt as $k => $val)
                                                <tr>
                                                    <td>
                                                        <select name="tax[state][]" class="form-control preselect">
                                                            <option value="Choose State">Select States</option>
                                                            @foreach ($states as $k => $state)
                                                                @php
                                                                    $FromTax = !empty($val['state']) ? $val['state'] : '';
                                                                    $FromState = !empty($state->name) ? $state->name : '';
                                                                    $disabled = !in_array($FromState, $finalarray) ? 'disabled' : '';
                                                                @endphp
                                                                <option
                                                                    {{ !empty($FromTax) && !empty($FromState) && $FromTax === $FromState ? 'selected' : '' }}
                                                                    value="{{ !empty($state->name) ? $state->name : '' }}">
                                                                    {{ !empty($state->name) ? $state->name : '' }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="number" min="0" step="0.001"
                                                            value="{{ $val['tax1'] ?? '' }}" name="tax[tax1][]"
                                                            class="form-control" placeholder="10">

                                                    </td>
                                                    <td>
                                                        <input type="text" value="{{ $val['tax_txt1'] ?? '' }}"
                                                            name="tax[tax_txt1][]"
                                                            class="form-control"placeholder="ex (GST or VAT)">
                                                    </td>
                                                    <td>
                                                        <input type="number" min="0" step="0.001"
                                                            value="{{ $val['tax2'] ?? '' }}" name="tax[tax2][]"
                                                            class="form-control" placeholder="10">

                                                    </td>
                                                    <td>
                                                        <input type="text" value="{{ $val['tax_txt2'] ?? '' }}"
                                                            name="tax[tax_txt2][]"
                                                            class="form-control"placeholder="ex (GST or VAT)">
                                                    </td>
                                                    <td>
                                                        <div class="col-md-2 form-group faq-add-main-btn">
                                                            {{-- <a href="" class="delete_text_record" data="{{ !empty($val['state']) ? $val['state'] : '' }}"> --}}
                                                            <i
                                                                class="tio-delete-outlined text-danger remove-product-faq-btn"></i>
                                                            {{-- </a> --}}
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex flex-wrap justify-content-end">
                                <a  href="{{ route('admin.business-settings.shipping-method.tax-calculation') }}"
                                    class="btn btn--primary px-4 mr-2">Back</a>
                                <button type="submit"
                                    class="btn btn--primary px-4">{{ \App\CPU\translate('Update') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(function() {

            $('.add-product-faq-btn').on('click', function() {

                $('#parent-faq-div').append(
                    `<tr class="inner-faq-div">
            <td>
                <select name="tax[state][]" class="form-control dynoselect" id="dynoselect">
                                                            <option value="Choose State" class="choose">Select States</option>
 
                @foreach ($states as $k => $state)
                    @php
                        $FromTax = !empty($val['state']) ? $val['state'] : '';
                        $FromState = !empty($state->name) ? $state->name : '';
                        $disabled = !in_array($FromState, $finalarray) ? 'disabled' : '';
                    @endphp
                    <option {{ $disabled }} class="dynamic"
                    {{ !empty($FromTax) && !empty($FromState) && $FromTax === $FromState ? 'selected' : '' }}
                        value="{{ !empty($state->name) ? $state->name : '' }}">
                        {{ !empty($state->name) ? $state->name : '' }}
                    </option>
                @endforeach
            </select>
            </td>
            <td>
                <input type="number" value="" min="0" step="0.001" name="tax[tax1][]" class="form-control" placeholder="10">
            </td>
            <td>
                <input type="text" value="" name="tax[tax_txt1][]" class="form-control"placeholder="ex (GST or VAT)">
            </td>
            <td>
                <input type="number" value="" min="0" step="0.001" name="tax[tax2][]" class="form-control" placeholder="10">
                
            </td>
            <td>
                <input type="text" value="" name="tax[tax_txt2][]" class="form-control"placeholder="ex (GST or VAT)">
            </td>
            <td>
                <div class="col-md-2 form-group faq-add-main-btn">
                    <i class="tio-delete-outlined text-danger remove-product-faq-btn"></i>
                </div>
            </td></tr>
            `
                );
            });

            $(document).on('click', '.remove-product-faq-btn', function() {
                // $(this).closest('.inner-faq-div').remove();
                this.parentElement.parentElement.parentElement.remove();
            });
            $(document).on("click", ".add-product-faq-btn", function() {
                $(".dynoselect").val($(".choose").val());
            });
            $(document).on("submit", ".update_tax", function(e) {
                $(".dynoselect, .preselect").each(function() {
                    if (this.value == "Choose State") {
                        e.preventDefault();
                        toastr.error('Empty field alert!');
                    }
                });
            });

        });
    </script>
@endpush
