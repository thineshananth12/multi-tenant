<ul class="pagination">
    @if ($tenants->currentPage() > 1)
        <li class="page-item">
            <a class="page-link" role="button" onclick="loadTenants({{ $tenants->currentPage() - 1 }})">Previous</a>
        </li>
    @endif
    @for ($i = 1; $i <= $tenants->lastPage(); $i++)
        <li class="page-item {{ ($tenants->currentPage() == $i) ? 'active' : '' }}">
            <a class="page-link" role="button" onclick="loadTenants({{ $i }})">{{ $i }}</a>
        </li>
    @endfor
    @if ($tenants->currentPage() < $tenants->lastPage())
        <li class="page-item">
            <a class="page-link" role="button" onclick="loadTenants({{ $tenants->currentPage() + 1 }})">Next</a>
        </li>
    @endif
</ul>