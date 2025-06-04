@forelse ($reports as $report)
    <tr>
        <td>{{ $report->title }}</td>
        <td>{{ $report->content }}</td>
        <td>
            <span class="badge 
                @switch($report->status)
                    @case(\App\Models\Report::SUBMITTED)
                        bg-primary
                        @break
                    @case(\App\Models\Report::PENDING)
                        bg-warning
                        @break
                    @case(\App\Models\Report::SUCCESS)
                        bg-success
                        @break
                    @case(\App\Models\Report::REJECTED)
                        bg-danger
                        @break
                    @case(\App\Models\Report::CANCELLED)
                        bg-secondary
                        @break
                    @default
                        bg-light
                @endswitch">
                {{ ucfirst($report->status) }}
            </span>
        </td>
        <td>
            <a href="{{ url('report/' . $report->id) }}" class="btn btn-primary btn-sm">
                <i class="la la-eye"></i>
            </a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="3">Tidak ada proyek</td>
    </tr>
@endforelse