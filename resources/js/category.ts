const colors = [
    "#413022",
    "#22db15",
    "#f7ff00",
    "#000fff",
];

const names = [
    "General",
    "Question",
    "NOTAM",
    "Event",
];

export class Category {
    id: number;

    private constructor(id: number) {
        this.id = id;
    }

    public static byName(name: String): Category {
        let id = 0;

        for (let comp of names) {
            if (name.localeCompare(comp, undefined, { sensitivity: 'base' }) === 0) {
                return new Category(id);
            }
            id++;
        }

        return null;
    }

    public static byId(id: number): Category {
        return new Category(id);
    }

    public getColor(): String {
        return colors[this.id];
    }

    public getName(): String {
        return names[this.id];
    }

    public getIndex(): number {
        return this.id;
    }
}
