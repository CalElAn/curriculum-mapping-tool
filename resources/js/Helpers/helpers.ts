import { router } from '@inertiajs/vue3';

export function autoGrow(event): void {
  event.target.style.height = '';
  event.target.style.height = event.target.scrollHeight + 'px';
}

export function getFilteredItems(route: string, filterValue: string): void {
  router.get(
    route,
    { filter: filterValue },
    { preserveState: false, replace: true },
  );
}
