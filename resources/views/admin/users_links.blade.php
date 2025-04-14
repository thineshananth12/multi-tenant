<ul class="pagination">
    @if ($users->currentPage() > 1)
        <li class="page-item">
            <a class="page-link" role="button" onclick="manageTenant('{{$tenantId}}',{{ $users->currentPage() - 1 }})">Previous</a>
        </li>
    @endif
    @for ($i = 1; $i <= $users->lastPage(); $i++)
        <li class="page-item {{ ($users->currentPage() == $i) ? 'active' : '' }}">
            <a class="page-link" role="button" onclick="manageTenant('{{$tenantId}}',{{ $i }})">{{ $i }}</a>
        </li>
    @endfor
    @if ($users->currentPage() < $users->lastPage())
        <li class="page-item">
            <a class="page-link" role="button" onclick="manageTenant('{{$tenantId}}',{{ $users->currentPage() + 1 }})">Next</a>
        </li>
    @endif
</ul>