<?php

namespace MaduLinux\RepositoryPattern\DataTransferObjects;

use Illuminate\Http\Request;

final class PaginationParameters
{
    public function __construct(
        public readonly int $per_page = 10,
        public readonly string $page_name = 'page',
        public readonly int $page = 1,
        public readonly array $columns = ['*'],
        public readonly ?string $search = null,
        public readonly array $searchable_fields = [],
        public readonly array $filters = [],
        public readonly array $filterable_fields = [],
        public readonly ?string $sort_by = null,
        public readonly ?string $sort_direction = null,
        public readonly array $sortable_fields = [],
        public readonly array $with = [],
        public readonly array $other = [],
    ) {}

    public static function fromArray(array $params): self
    {
        return new self(
            per_page: $params['per_page'] ?? 10,
            page_name: $params['page_name'] ?? 'page',
            page: $params['page'] ?? 1,
            columns: $params['columns'] ?? ['*'],
            search: $params['search'] ?? null,
            searchable_fields: $params['searchable_fields'] ?? [],
            filters: $params['filters'] ?? [],
            filterable_fields: $params['filterable_fields'] ?? [],
            sort_by: $params['sort_by'] ?? null,
            sort_direction: $params['sort_direction'] ?? null,
            sortable_fields: $params['sortable_fields'] ?? [],
            with: $params['with'] ?? [],
            other: $params['other'] ?? [],
        );
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            per_page: $request->input('per_page', 10),
            page_name: $request->input('page_name', 'page'),
            page: $request->input('page', 1),
            columns: $request->input('columns', ['*']),
            search: $request->input('search', null),
            searchable_fields: $request->input('searchable_fields', []),
            filters: $request->input('filters', []),
            filterable_fields: $request->input('filterable_fields', []),
            sort_by: $request->input('sort_by', null),
            sort_direction: $request->input('sort_direction', null),
            sortable_fields: $request->input('sortable_fields', []),
            with: $request->input('with', []),
            other: $request->input('other', []),
        );
    }

    public function toArray(): array
    {
        return [
            'per_page' => $this->per_page,
            'page_name' => $this->page_name,
            'page' => $this->page,
            'columns' => $this->columns,
            'search' => $this->search,
            'searchable_fields' => $this->searchable_fields,
            'filters' => $this->filters,
            'filterable_fields' => $this->filterable_fields,
            'sort_by' => $this->sort_by,
            'sort_direction' => $this->sort_direction,
            'sortable_fields' => $this->sortable_fields,
            'with' => $this->with,
            'other' => $this->other,
        ];
    }
}