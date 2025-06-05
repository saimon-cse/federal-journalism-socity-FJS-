@extends('layouts.admin.app')

@section('title', 'Financial Ledger')
@section('page-title', 'Financial Ledger')

@section('header-actions')
    @canany(['record-income', 'record-expense', 'transfer-funds-between-accounts'])
        <a href="{{ route('admin.financial-ledgers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> New Ledger Entry
        </a>
    @endcanany
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Ledger Entries</h3>
            <div class="card-tools">
                <form method="GET" action="{{ route('admin.financial-ledgers.index') }}" class="form-inline flex-wrap">
                    <div class="form-group mr-2 mb-2">
                        <label for="search_description_filter" class="mr-1 sr-only">Desc:</label>
                        <input type="text" name="search_description" id="search_description_filter" class="form-control form-control-sm" placeholder="Search Description..." value="{{ request('search_description') }}">
                    </div>
                    <div class="form-group mr-2 mb-2">
                        <label for="entry_type_filter" class="mr-1 sr-only">Type:</label>
                        <select name="entry_type" id="entry_type_filter" class="form-select form-select-sm">
                            <option value="">All Types</option>
                            @foreach($entryTypes as $type)
                                <option value="{{ $type }}" {{ request('entry_type') == $type ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ',$type)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mr-2 mb-2">
                        <label for="category_id_filter" class="mr-1 sr-only">Category:</label>
                        <select name="category_id" id="category_id_filter" class="form-select form-select-sm">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mr-2 mb-2">
                        <label for="payment_account_id_filter" class="mr-1 sr-only">Account:</label>
                        <select name="payment_account_id" id="payment_account_id_filter" class="form-select form-select-sm">
                            <option value="">All Accounts</option>
                            @foreach($paymentAccounts as $acc)
                                <option value="{{ $acc->id }}" {{ request('payment_account_id') == $acc->id ? 'selected' : '' }}>{{ $acc->account_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mr-2 mb-2">
                        <label for="start_date_filter" class="mr-1 sr-only">From:</label>
                        <input type="date" name="start_date" id="start_date_filter" class="form-control form-control-sm" value="{{ request('start_date') }}" title="Start Date">
                    </div>
                    <div class="form-group mr-2 mb-2">
                        <label for="end_date_filter" class="mr-1 sr-only">To:</label>
                        <input type="date" name="end_date" id="end_date_filter" class="form-control form-control-sm" value="{{ request('end_date') }}" title="End Date">
                    </div>
                    <button type="submit" class="btn btn-sm btn-default mb-2"><i class="fas fa-search"></i> Filter</button>
                    <a href="{{ route('admin.financial-ledgers.index')}}" class="btn btn-sm btn-outline-secondary ml-2 mb-2">Reset</a>
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Description</th>
                            <th>Type</th>
                            <th>Category</th>
                            <th>Amount (BDT)</th>
                            <th>From Account</th>
                            <th>To Account</th>
                            <th>Recorded By</th>
                            <th>Ref. Payment</th>
                            {{-- Add Actions if Edit/Delete are implemented --}}
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ledgers as $ledger)
                            <tr>
                                <td>{{ $ledger->transaction_datetime->format('d M Y, h:i A') }} <br> <small class="text-muted">{{ $ledger->ledger_entry_uuid }}</small></td>
                                <td style="white-space: normal; min-width: 200px;">{{ $ledger->description }}
                                    @if($ledger->external_reference_id) <br><small class="text-muted">Ref: {{ $ledger->external_reference_id }}</small>@endif
                                    @if($ledger->external_party_name) <br><small class="text-muted">Party: {{ $ledger->external_party_name }}</small>@endif
                                </td>
                                <td>
                                     @if($ledger->entry_type == 'income') <span class="badge badge-success">
                                     @elseif($ledger->entry_type == 'expense') <span class="badge badge-danger">
                                     @elseif($ledger->entry_type == 'transfer') <span class="badge badge-info">
                                     @elseif($ledger->entry_type == 'opening_balance') <span class="badge badge-primary">
                                     @else <span class="badge badge-secondary"> @endif
                                        {{ ucfirst(str_replace('_', ' ', $ledger->entry_type)) }}</span>
                                </td>
                                <td>{{ $ledger->category->name ?? 'N/A' }}</td>
                                <td class="text-right">
                                    @if(in_array($ledger->entry_type, ['income', 'opening_balance']))
                                        <span class="text-success font-weight-bold">+{{ number_format($ledger->amount, 2) }}</span>
                                    @elseif($ledger->entry_type == 'expense')
                                        <span class="text-danger font-weight-bold">-{{ number_format($ledger->amount, 2) }}</span>
                                    @else {{-- Transfer or Adjustment --}}
                                        {{ number_format($ledger->amount, 2) }}
                                    @endif
                                </td>
                                <td>{{ $ledger->fromPaymentAccount->account_name ?? 'N/A' }}</td>
                                <td>{{ $ledger->toPaymentAccount->account_name ?? 'N/A' }}</td>
                                <td>{{ $ledger->recordedByUser->name ?? 'System' }}</td>
                                <td>
                                    @if($ledger->payment)
                                        <a href="{{ route('admin.payments.show', $ledger->payment_id) }}" target="_blank" title="View Payment">{{ Str::limit($ledger->payment->payment_uuid, 8, '...') }} <i class="fas fa-external-link-alt fa-xs"></i></a>
                                    @else
                                        N/A
                                    @endif
                                </td>

                                <td>
                                    @can('edit-financial-ledgers')
                                    <a href="route('admin.financial-ledgers.edit', $ledger->id)" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                                    @endcan
                                    @can('delete-financial-ledgers')
                                    <form action="route('admin.financial-ledgers.destroy', $ledger->id)" method="POST" class="d-inline" onsubmit="return confirm('Are you sure? This action might be irreversible.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                    @endcan
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No ledger entries found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $ledgers->links() }}
            </div>
        </div>
    </div>
@endsection
