import { ref, toValue } from 'vue';
import  remove  from 'lodash/remove';

export function useFormHelpers(
  initialSubformItems: Array,
  newSubformItem: Object,
) {
  const subformItems = ref(initialSubformItems);
  const shouldAllowAdd = ref(true);

  function add(): void {
    subformItems.value.unshift({
      ...toValue(newSubformItem),
      adding: true,
    });

    shouldAllowAdd.value = false;
  }

  function onCancelAdd(): void {
    subformItems.value.shift();
    shouldAllowAdd.value = true;
  }

  function onDestroyed(index): void {
    remove(subformItems.value, (item, itemIndex) => itemIndex === index);
  }

  return { subformItems, shouldAllowAdd, add, onCancelAdd, onDestroyed };
}
