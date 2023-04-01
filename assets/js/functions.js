export const pick = (obj, keys) => {
    if (obj === undefined) {
        return
    }
    let result = {}
    for (let key of keys) {
        if (obj[key] !== undefined) {
            result[key] = obj[key]
        }
    }
    return result
}