<ul>
    @foreach ($children as $child)
      <li>
        <div class="card-position">
          <div class="name">{{ $child->name }}</div>
          @php
            $assignedUsers =  $child->user ? $child->user->name : '-';
          @endphp

          <div class="user">{{ __('base.assigned_to') }}: {{ $assignedUsers ?: '-' }}</div>
        </div>
  
        @if ($child->childrenRecursive->count())
          @include('positions.structure-child', ['children' => $child->childrenRecursive])
        @endif
      </li>
    @endforeach
  </ul>
  