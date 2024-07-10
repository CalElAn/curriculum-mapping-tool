import { ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { toast, deleteConfirmationDialog } from '@/Components/swal.js';

export const emittedEvents = ['cancelAdd', 'stored', 'destroyed'] as const;

export function useSubformHelpers(
  subformData: object,
  useFormData: object,
  emitFunction: (event: (typeof emittedEvents)[number], ...args: any[]) => void,
  storeRoute: string,
  updateRouteName: string,
  initialUpdateRouteParams: Array,
  destroyRouteName: string,
) {
  const id = ref(subformData.id);

  const form: object & InertiaFormProps<object> = useForm(useFormData);

  const adding = ref(subformData.adding ?? false);
  const editing = ref(false);

  function store(): void {
    form.post(storeRoute, {
      onSuccess: () => {
        adding.value = false;
        id.value = usePage().props.session.data.id;
        emitFunction('stored');
        toast.fire({ title: `Added!` });
      },
    });
  }

  function update(): void {
    form.patch(
      route(updateRouteName, [...initialUpdateRouteParams, id.value]),
      {
        onSuccess: () => {
          toast.fire({ title: `Saved!` });
        },
      },
    );
  }

  function destroy(): void {
    deleteConfirmationDialog(() =>
      form.delete(route(destroyRouteName, id.value), {
        onSuccess: () => {
          emitFunction('destroyed');
          toast.fire({ title: `Deleted!` });
        },
      }),
    );
  }

  return { form, adding, editing, store, update, destroy, id };
}
